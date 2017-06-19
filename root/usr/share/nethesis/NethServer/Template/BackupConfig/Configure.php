<?php


echo $view->header()->setAttribute('template', $T('Configure_Header'));

echo $view->slider('HistoryLength')->setAttribute('min', 1)->setAttribute('max', 31);

echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_CANCEL | $view::BUTTON_HELP);
