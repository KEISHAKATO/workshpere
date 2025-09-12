// resources/js/app.js
import './bootstrap';

import './gmaps-autocomplete';
import './reports'
import 'flowbite';

// (Optional) ensure Google’s PAC dropdown isn’t hidden behind modals/nav
const style = document.createElement('style');
style.textContent = `.pac-container{z-index:2147483647 !important;}`;
document.head.appendChild(style);
