from time import *
import sys
sys.path.insert(0, '/home/pi/PointRFID/Script/I2C-LCD/')
import lcddriver

lcd = lcddriver.lcd()
lcd.lcd_clear()
lcd.lcd_display_string(" IT", 1)
lcd.lcd_display_string(" WORKS", 2)
