#include "console.h"
#include "fb.h"

#include <assert.h>
#include <string.h>

#define SCREEN_WIDTH 20
#define SCREEN_HEIGHT 4

#define SCREEN_LIMIT (SCREEN_WIDTH * SCREEN_HEIGHT)

static uint32_t glyphs[SCREEN_LIMIT];

static uint32_t cursor = 0;

int utf8_get_codepoint(const char **text, size_t *len);

static void console_flush()
{
  for (size_t i = 0; i < SCREEN_LIMIT; i++)
  {
    fb_set_node(i, glyphs[i]);
  }
  fb_flush_all();
}

void console_init()
{
  fb_home_all();
  console_clear(false);
  cursor = 0;
}

void console_newline()
{
  cursor = SCREEN_WIDTH * ((cursor + SCREEN_WIDTH - 1) / SCREEN_WIDTH);
}

void console_write(char const *text, size_t length)
{
  uint32_t cp;
  while ((cp = utf8_get_codepoint(&text, &length)) != 0)
  {
    if (cp == '\n')
    {
      console_newline();
      continue;
    }

    if (cursor >= SCREEN_LIMIT)
    {
      memmove(&glyphs[0], &glyphs[SCREEN_WIDTH], (SCREEN_HEIGHT - 1) * SCREEN_WIDTH);
      for (size_t j = 0; j < SCREEN_WIDTH; j++)
      {
        glyphs[(SCREEN_HEIGHT - 1) * SCREEN_WIDTH + j] = ' ';
      }
      cursor = (SCREEN_HEIGHT - 1) * SCREEN_WIDTH;
    }
    assert(cursor < SCREEN_LIMIT);

    glyphs[cursor] = cp;
    cursor += 1;
  }
  console_flush();
}

void console_setCursor(size_t x, size_t y)
{
  cursor = y * SCREEN_WIDTH + x;
}

void console_clear(bool flush)
{
  cursor = 0;
  for (size_t i = 0; i < SCREEN_LIMIT; i++)
  {
    glyphs[i] = ' ';
  }
  if (flush)
    console_flush();
}

/**
 * Reads a character from the string given utf-8 string.
 **/
int utf8_get_codepoint(const char **text, size_t *len)
{
#define next() (((*len) > 0) ? ((*len)--, (*(*text)++)) : 0)
#define ERROR 0x00

  int codepoint;
  char c0 = next();
  if (c0 & 0x80)
  {
    // utf8 char
    if ((c0 & 0xC0) == 0x80)
    {
      return ERROR; // this is not what we wanted to have...
    }
    if ((c0 & 0xE0) == 0xC0)
    {
      // two byte
      char c1 = next();
      if ((c1 & 0xC0) != 0x80)
      {
        return '?'; // parse error
      }
      codepoint = (c1 & 0x3F) | ((c0 & 0x1F) << 6);
    }
    else if ((c0 & 0xF0) == 0xE0)
    {
      // three byte
      char c1 = next();
      char c2 = next();
      if ((c1 & 0xC0) != 0x80)
      {
        return ERROR; // parse error
      }
      if ((c2 & 0xC0) != 0x80)
      {
        return ERROR; // parse error
      }
      codepoint = (c2 & 0x3F) | (((c1 & 0x3F) | ((c0 & 0x0F) << 6)) << 6);
    }
    else if ((c0 & 0xF8) == 0xF0)
    {
      // four byte
      char c1 = next();
      char c2 = next();
      char c3 = next();
      if ((c1 & 0xC0) != 0x80)
      {
        return ERROR; // parse error
      }
      if ((c2 & 0xC0) != 0x80)
      {
        return ERROR; // parse error
      }
      if ((c3 & 0xC0) != 0x80)
      {
        return ERROR; // parse error
      }
      codepoint = (c3 & 0x3F) | (((c2 & 0x3F) | (((c1 & 0x3F) | ((c0 & 0x07) << 6)) << 6)) << 6);
    }
    else
    {
      return ERROR; // parse error
    }
  }
  else
  {
    codepoint = c0; // ASCII
  }
  return codepoint;
#undef next
#undef ERROR
}