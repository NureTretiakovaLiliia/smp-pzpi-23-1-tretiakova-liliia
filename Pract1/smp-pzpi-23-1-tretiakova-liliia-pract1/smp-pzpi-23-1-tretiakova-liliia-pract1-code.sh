#!/bin/bash

if [ "$#" -ne 2 ]; then echo "Expected 2 args" >&2; exit 1; fi
if ! [[ "$1" =~ ^[0-9]+$ ]] || [ "$1" -lt 8 ]; then echo "Invalid args: height must be at least 8" >&2; exit 2; fi
if ! [[ "$2" =~ ^[0-9]+$ ]] || [ "$2" -lt 7 ]; then echo "Invalid args: width must be at least 7" >&2; exit 3; fi
if (( $1 < $2 )); then echo "Invalid args" >&2; exit 4; fi

height=$1
width=$2

if [ $(( height % 2 )) -ne 0 ]; then height=$(( height - 1 )); fi
if [ $(( width % 2 )) -eq 0 ]; then width=$(( width - 1 )); fi

half_height=$(( (height - 1) / 2 ))

if [ $(( width - (2 * half_height - 1) )) -ne 2 ]; then echo "Cannot draw a tree" >&2; exit 4; fi

center_text() {
  local text="$1"
  local padding=$(( (width - ${#text}) / 2 ))

  padding_str=""

  for _ in $(seq 1 "$padding"); do
    padding_str+=" "
  done

  echo "$padding_str$text"
}

last_pattern=""
for h in $(seq 1 $half_height); do
  symbols=$(( 2 * h - 1 ))
  pattern=$([[ $((h % 2)) -eq 1 ]] && echo "*" || echo "#")

  line=""
  for i in $(seq 1 $symbols); do
    line+="$pattern"
  done

  center_text "$line"

  last_pattern="$pattern"
done

if [ "$last_pattern" = "*" ]; then
  second_layer_pattern="#"
else
  second_layer_pattern="*"
fi

i=2
until [ "$i" -gt "$half_height" ]; do
  symbols=$(( 2 * i - 1 ))
  line=""
  for _ in $(seq 1 "$symbols"); do
    line+="$second_layer_pattern"
  done
  center_text "$line"

  if [ "$second_layer_pattern" = "*" ]; then
    second_layer_pattern="#"
  else
    second_layer_pattern="*"
  fi

  ((i++))
done

j=0
while [ "$j" -lt 2 ]; do
  center_text "###"
  ((j++))
done

for char in '*'; do
  line=""
  for ((i=1; i<=width; i++)); do
    line+="$char"
  done
  echo "$line"
done
