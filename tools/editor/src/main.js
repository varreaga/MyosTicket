/*
 * MiHelpdesk — Editor de texto. Punto de entrada del archivo compilado
 * js/mh-editor.js. Parte de MiHelpdesk, fork de osTicket bajo GPLv2.
 */
import './editor.css';
import { MHEditor } from './editor.js';
import { install, createEditor } from './osticket.js';

window.MHEditor = MHEditor;
window.MHEditor.create = createEditor;
install();
