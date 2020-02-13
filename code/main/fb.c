#include "fb.h"

#include "driver/uart.h"
#include "driver/gpio.h"

#define ECHO_TEST_TXD (GPIO_NUM_17)
#define ECHO_TEST_RXD (GPIO_NUM_16)
#define ECHO_TEST_RTS (UART_PIN_NO_CHANGE)
#define ECHO_TEST_CTS (UART_PIN_NO_CHANGE)

static uint8_t fb_nodeSymbols[256];

//! Translates a unicode codepoint to a Fallblattanzeigen-Display.
uint8_t fb_map_character(uint32_t codepoint)
{
  // clang-format off
    switch(codepoint) {
        case '0': return 0x21; 
        case '1': return 0x22;
        case '2': return 0x23;
        case '3': return 0x24;
        case '4': return 0x25;
        case '5': return 0x26;
        case '6': return 0x27;
        case '7': return 0x28;
        case '8': return 0x29;
        case '9': return 0x2A;
        
        case 'A': return 0x2B;
        case 'B': return 0x2C;
        case 'C': return 0x2D;
        case 'D': return 0x2E;
        case 'E': return 0x2F;
        case 'F': return 0x30;
        case 'G': return 0x31;
        case 'H': return 0x32;
        case 'I': return 0x33;
        case 'J': return 0x34;
        case 'K': return 0x35;
        case 'L': return 0x36;
        case 'M': return 0x37;
        case 'N': return 0x38;
        case 'O': return 0x39;
        case 'P': return 0x3A;
        case 'Q': return 0x3B;
        case 'R': return 0x3C;
        case 'S': return 0x3D;
        case 'T': return 0x3E;
        case 'U': return 0x3F;
        case 'V': return 0x40;
        case 'W': return 0x41;
        case 'X': return 0x42;
        case 'Y': return 0x43;
        case 'Z': return 0x44;
        
        case 'a': return 0x2B;
        case 'b': return 0x2C;
        case 'c': return 0x2D;
        case 'd': return 0x2E;
        case 'e': return 0x2F;
        case 'f': return 0x30;
        case 'g': return 0x31;
        case 'h': return 0x32;
        case 'i': return 0x33;
        case 'j': return 0x34;
        case 'k': return 0x35;
        case 'l': return 0x36;
        case 'm': return 0x37;
        case 'n': return 0x38;
        case 'o': return 0x39;
        case 'p': return 0x3A;
        case 'q': return 0x3B;
        case 'r': return 0x3C;
        case 's': return 0x3D;
        case 't': return 0x3E;
        case 'u': return 0x3F;
        case 'v': return 0x40;
        case 'w': return 0x41;
        case 'x': return 0x42;
        case 'y': return 0x43;
        case 'z': return 0x44;
        
        case 0xC4: // Ä
        case 0xE4: // ä
            return 0x45;

        case 0xD6: // Ö
        case 0xF6: // ö
            return 0x46;
        
        case 0xDC: // Ü
        case 0xFC: // ü
            return 0x47;
        
        case ' ': return 0x20;
        case '-': return 0x48;
        case '.': return 0x49;
        case '(': return 0x4A;
        case ')': return 0x4B;
        case '!': return 0x4C;
        case ':': return 0x4D;
        case '/': return 0x4E;
        case '"': return 0x4F;
        case ',': return 0x50;
        case '=': return 0x51;

        case 0xC5: // Å
        case 0xE5: // å
        case 0x212B: // Å
            return 0x52; 
        
        case 0xD8: // Ø
        case 0xF8: // ø
            return 0x53;

        // default handler: return '.' as a replacement character
        default: return 0x49; 
    }
  // clang-format on
}

//! Sets a single node to a certain code point.
void fb_set_node(uint8_t address, uint32_t codepoint)
{
  char buf[3] = {
      0x88,
      address,
      fb_map_character(codepoint),
  };
  uart_write_bytes(UART_NUM_1, buf, 3);
  fb_nodeSymbols[address] = buf[2];
}

void fb_flush_all()
{
  uart_write_bytes(UART_NUM_1, "\x81", 3);
}

void fb_home_all()
{
  uart_write_bytes(UART_NUM_1, "\x82", 3);
  for (size_t i = 0; i < 256; i++)
  {
    fb_nodeSymbols[i] = fb_map_character(' ');
  }
}

void fb_lock_module(uint8_t address)
{
  char buf[2] = {0x86, address};
  uart_write_bytes(UART_NUM_1, buf, 2);
}

void fb_unlock_module(uint8_t address)
{
  char buf[2] = {0x87, address};
  uart_write_bytes(UART_NUM_1, buf, 2);
}

void fb_init()
{
  /* Configure parameters of an UART driver,
     * communication pins and install the driver */
  uart_config_t uart_config = {
      .baud_rate = 4800,
      .data_bits = UART_DATA_8_BITS,
      .parity = UART_PARITY_EVEN,
      .stop_bits = UART_STOP_BITS_2,
      .flow_ctrl = UART_HW_FLOWCTRL_DISABLE,
      .source_clk = UART_SCLK_APB,
  };
  uart_driver_install(
      UART_NUM_1, // Device
      1024,       // rx_buffer_size
      0,          // tx_buffer_size
      0,          // queue_size
      NULL,       // QueueHandle
      0           // intr_alloc_flags
  );
  uart_param_config(UART_NUM_1, &uart_config);
  uart_set_pin(UART_NUM_1, ECHO_TEST_TXD, ECHO_TEST_RXD, ECHO_TEST_RTS, ECHO_TEST_CTS);
}