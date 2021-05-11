
const $ = require("jquery");
global.$ = global.jQuery = $;
import "bootstrap";

import "bootstrap/dist/css/bootstrap.css";

import './styles/app.scss';
$('[data-toggle="popover"]').popover();

// start the Stimulus application
import './bootstrap';
import('bootstrap-icons/font/bootstrap-icons.css');