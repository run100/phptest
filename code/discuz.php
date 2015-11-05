<?php

 // 清除冗余discuz代码
  public static function cleanDiscuzCode($message) {

      $message = strip_tags($message);
      $message = preg_replace("/attach:\/\/(\d+)\.?(\w*)/ie", "", $message);
      $message = preg_replace("/\{:([^\[\<]+?):\}/ie", "", $message);
      $message = preg_replace("/\[url(=((https?|ftp|gopher|news|telnet|rtsp|mms|callto|bctp|thunder|qqdl|synacast){1}:\/\/|www\.|mailto:)?([^\r\n\[\"']+?))?\](.+?)\[\/url\]/ies", "", $message);
      $message = str_replace(array(
      '[/color]', '[/backcolor]', '[/size]', '[/font]', '[/align]', '[b]', '[/b]', '[s]', '[/s]', '[hr]', '[/p]',
      '[i=s]', '[i]', '[/i]', '[u]', '[/u]', '[list]', '[list=1]', '[list=a]',
      '[list=A]', "\r\n[*]", '[*]', '[/list]', '[indent]', '[/indent]', '[/float]'
      ), array(
      '', '', '', '', '', '', '', '', '', '', '', '', '',
      '', '', '', '', '', '',
      '', '', '', '', '', '', ''
      ), preg_replace(array(
      "/\[color=([#\w]+?)\]/i",
      "/\[color=(rgb\([\d\s,]+?\))\]/i",
      "/\[backcolor=([#\w]+?)\]/i",
      "/\[backcolor=(rgb\([\d\s,]+?\))\]/i",
      "/\[size=(\d{1,2}?)\]/i",
      "/\[size=(\d{1,2}(\.\d{1,2}+)?(px|pt)+?)\]/i",
      "/\[font=([^\[\<]+?)\]/i",
      "/\[align=(left|center|right)\]/i",
      "/\[p=(\d{1,2}|null), (\d{1,2}|null), (left|center|right)\]/i",
      "/\[float=left\]/i",
      "/\[float=right\]/i",

      ), "", $message));
      $message = preg_replace("/\s?\[quote\][\n\r]*(.+?)[\n\r]*\[\/quote\]\s?/is", "", $message);
      $message = preg_replace("/\s?\[attach\][\n\r]*(.+?)[\n\r]*\[\/attach\]\s?/is", "", $message);
      $message = preg_replace("/\s?\[img\](.+?)\[\/img\]\s?/is", "", $message);
      return $message;

  }
