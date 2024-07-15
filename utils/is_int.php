<?php

function isInt($num) {

  if (
    !isset($num) || !is_numeric($num) ||
    !is_int((int)$num)
  ) {
    return false;
  }

  return true;
  
}
