#pragma once

#include <stddef.h>
#include <stdbool.h>

void console_init();

//! Takes a utf-8 encoded string and prints it to the anzeige.
void console_write(char const *text, size_t length);

//! Clears the full console
void console_clear(bool flush);

//! Enforces that the cursor will be aligned to a newline.
//! This function will not insert a newline if you are already at the start of the line.
void console_newline();

//! Sets the cursor position
void console_setCursor(size_t x, size_t y);