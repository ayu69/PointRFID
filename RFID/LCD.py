import lcddriver
from time import *

lcd = lcddriver.lcd()
lcd.lcd_clear()
lcd.lcd_display_string(" Go Tronic", 1)
lcd.lcd_display_string(" I2C Serial LCD", 2)
