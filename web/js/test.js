$(document).ready(function () {
    var socketConnect = function () {

        var socket = new WebSocket('ws://webtask.future-processing.com:8068/ws/currencies');

        socket.onmessage = function (msg) {
            var requestSaveMsg = $.ajax()
            var data = JSON.parse(msg.data);
            currenciesData(data);
        };

        socket.onclose = function (event) {
            if (event.code !== 1000) {
                $('.btn-sell').remove();
                currencyBody.empty();
                currencyBody.append('<p style="color: red">Error: failed to connect to Api, server not responding.</p>')
            }
        };

    };

    socketConnect();
});