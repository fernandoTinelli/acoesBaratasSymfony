import { Controller } from '@hotwired/stimulus';

import Chart from 'chart.js/auto';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    static values = {
        graph1: String
    };

    connect() {
    }
}
