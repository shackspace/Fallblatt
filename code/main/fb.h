#pragma once

#include <stdint.h>

//! Initializes the UART to the Fallblattanzeige.
void fb_init();

//! Sets a single node to a certain code point.
void fb_set_node(uint8_t address, uint32_t codepoint);

//! Starts all nodes to roll to the currently selected character.
void fb_flush_all();

//! Start the homing cycle.
void fb_home_all();

//! Locks a module so it won't change anymore when doing a flush/clear.
void fb_lock_module(uint8_t address);

//! Unlocks a module so it will change again when doing a flush/clear.
void fb_unlock_module(uint8_t address);