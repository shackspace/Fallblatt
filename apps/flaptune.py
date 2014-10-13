#!/usr/bin/env python3
import urllib.parse
import urllib.request
import subprocess
import time

# Display a fortune message
# requires python3 and fortune 
# written under the beerware license
# by momorientes

def main():
    clear()
    time.sleep(5)
    f = fortune()
    print(f)
    buffered_send(f)

def send_text(text,position):
    url = "http://flap.shack/index.php?char=" + text + "&action=char&position="+ str(position) + "&rotate=true"
    u = urllib.request.urlopen(url)

def fortune():
    cmd = ['fortune', '-n', '80', '-s']
    fortune = subprocess.check_output(cmd)
    fortune = fortune.decode("utf-8")
    fortune = fortune.split('\n')
    return fortune[0]

def buffered_send(text):
    for i in range(0,len(text)):
        data = urllib.parse.quote_plus(text[i])
        send_text(data, i)
        time.sleep(0.2)

def clear():
    url = "http://flap.shack/index.php?action=reset"
    urllib.request.urlopen(url)

if __name__ == "__main__":
    while True:
        main()
        time.sleep(30)
