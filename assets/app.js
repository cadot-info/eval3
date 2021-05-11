
const $ = require("jquery");
global.$ = global.jQuery = $;
import "bootstrap";

import "bootstrap/dist/css/bootstrap.css";

import './styles/app.scss';
$('[data-toggle="popover"]').popover();
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

// start the Stimulus application
import './bootstrap';