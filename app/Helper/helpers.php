<?php

/** buat bikin menu sidebar yg dipilih jd aktif */
function set_active($route) {
    if (is_array($route )){
        return in_array(Request::path(), $route) ? 'active' : '';
    }
    return Request::path() == $route ? 'active' : '';
}

/** buat nampilin menu sidebar yg lg dibuka */
function set_show($route) {
     if (is_array($route )){
        return in_array(Request::path(), $route) ? 'show' : '';
    }
  return Request::path() == $route ? 'show' : '';
}
