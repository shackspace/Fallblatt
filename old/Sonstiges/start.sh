curl --silent http://localhost/index.php\?text=just\+try\+it\+\+\+\+\+\+\+\+\+flap.shack\+\+\+\+\+\+\+\+\+\+flap.shack\%2Fdirect.\+\+\+\+\+\+\+\+\+\+\+\+\+\+\+\+\+\+html > /dev/null

while true; do
  if [[ $(($(date -r /dev/ttyAMA0 +%s) + $(cat /home/pi/delay))) < $(date +%s) ]]; then
    curl --silent http://localhost/index.php\?text=just\+try\+it\+\+\+\+\+\+\+\+\+flap.shack\+\+\+\+\+\+\+\+\+\+flap.shack\%2Fdirect.\+\+\+\+\+\+\+\+\+\+\+\+\+\+\+\+\+\+html > /dev/null;
  fi
  sleep 1;
done;
