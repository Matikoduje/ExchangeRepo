$(document).ready(function () {

    var walletBody = $('#walletBody');
    var currencyBody = $('#currencyBody');
    var body = $('body');
    var time;

    var currenciesData = function (data) {
        var pln = Number($('#pln').text());
        time = data.PublicationDate;
        var x = new Date(time);
        $('.time-info').empty();
        $('.time-info').text(x);
        $.each(data.Items, function (index, value) {

            var code = value.Code;
            var stringCodeCurrency = code.toLowerCase() + 'Currency';
            var stringCodeWallet = code.toLowerCase() + 'Wallet';
            var roundedSellPrice = Number(value.SellPrice.toFixed(2));
            var roundedPurchasePrice = Number(value.PurchasePrice.toFixed(2));
            var amount = Number($(`#${stringCodeWallet} td:nth-child(3)`).text());
            if (code === 'RUB' || code === 'CZK') {
                amount = amount/100;
            }
            var result = amount * roundedPurchasePrice;
            result = result.toFixed(2);

            $(`#${stringCodeCurrency} td:nth-child(3)`).text(roundedSellPrice);
            $(`#${stringCodeWallet} td:nth-child(2)`).text(roundedPurchasePrice);
            $(`#${stringCodeWallet} td:nth-child(4)`).text(result);

            if (pln > roundedPurchasePrice) {
                $(`#${stringCodeCurrency} td:nth-child(4)`).empty();
                $(`#${stringCodeCurrency} td:nth-child(4)`).append('<button class="btn-sm btn-info btn-buy" data-toggle="modal" data-target="#myModal">Buy</button>');
            }

            if ($('#myModal').hasClass(`sell ${code}`)) {
                $('.sellSpan').text(roundedPurchasePrice);
            }

            if ($('#myModal').hasClass(`buy ${code}`)) {
                $('.buySpan').text(roundedSellPrice);
            }
        });
    };

    var walletData = function (response) {

        var button = '<button class="btn-sm btn btn-info btn-sell" data-toggle="modal" data-target="#myModal">Sell</button>';
        $('#pln').text(response.PLN);
        $('#czkWallet td:nth-child(3)').text(response.CZK*100);
        if (response.CZK > 0) {
            $('#czkWallet td:nth-child(5)').append(button);
        }
        $('#eurWallet td:nth-child(3)').text(response.EUR);
        if (response.EUR > 0) {
            $('#eurWallet td:nth-child(5)').append(button);
        }
        $('#usdWallet td:nth-child(3)').text(response.USD);
        if (response.USD > 0) {
            $('#usdWallet td:nth-child(5)').append(button);
        }
        $('#gbpWallet td:nth-child(3)').text(response.GBP);
        if (response.GBP > 0) {
            $('#gbpWallet td:nth-child(5)').append(button);
        }
        $('#rubWallet td:nth-child(3)').text(response.RUB*100);
        if (response.RUB > 0) {
            $('#rubWallet td:nth-child(5)').append(button);
        }
        $('#chfWallet td:nth-child(3)').text(response.CHF);
        if (response.CHF > 0) {
            $('#chfWallet td:nth-child(5)').append(button);
        }
    };

    var getWalletData = function () {

        var requestGetData = $.ajax({
            url: "api/wallet",
            type: "get",
            dataType: "json"
        });

        requestGetData.fail(function (jqXHR) {
            if (jqXHR.status === 404) {
                walletBody.empty();
                walletBody.append('<p style="color: red">Error: failed to connect, server not responding.</p>')
            }
        });

        requestGetData.done(function (response) {
            if (response.isActive === 'false') {
                walletBody.empty();
                walletBody.append('<p style="color: red">Please add funds to your wallet</p>')
            }
            walletData(response);
            socketConnect();
        });

    };

    var socketConnect = function () {

        var socket = new WebSocket('ws://webtask.future-processing.com:8068/ws/currencies');

        socket.onmessage = function (msg) {
            var requestSave = $.ajax({
                url: "api/save",
                dataType: "json",
                type: "get"
            });

            requestSave.done(function () {
                var data = JSON.parse(msg.data);
                currenciesData(data);
            });

            requestSave.fail(function () {

            });
        };

        socket.onclose = function (event) {
            if (event.code !== 1000) {
                $('.btn-sell').remove();
                currencyBody.empty();
                currencyBody.append('<p style="color: red">Error: failed to connect to Api, server not responding.</p>')
            }
        };

    };

    getWalletData();

    body.on('click', '.btn-sell', function (e) {

        var currency = this.parentNode.parentNode.childNodes[1].innerText;
        var sellValue = this.parentNode.parentNode.childNodes[3].innerText;
        var amount = this.parentNode.parentNode.childNodes[5].innerText;
        var account = Number($('#pln').text());
        var accountBalance = account + Number(sellValue);
        accountBalance = accountBalance.toFixed(2);

        $('#myModal').removeClass();
        $('#myModal').addClass('modal fade');
        $('#myModal').addClass(`sell ${currency}`);
        var modal = $('.modal-body');
        modal.empty();

        var sellInput = $(`<div class="form-group"><label for="currency">How much ${currency} you want to sell: </label>` +
            `<input type="number" value=1 name=${currency} min=1 max=${amount} class="form-control" id="currency"></div>`);

        var modalContent = $(`<div><p>Current ${currency} sell value: <span class="sellSpan">${sellValue}</span> PLN</p><p>Transaction value: <span class="transactionValue">${sellValue}</span> PLN</p><p>Account balance after transaction <span class="accountBalance">${accountBalance}</span> PLN</p></div>`);
        modalContent.append(sellInput);
        $('.modal-title').text(`Sell ${currency}`);
        modalContent.append('<p class="modal-errors" style="color: red"></p>');
        modal.append(modalContent);

        $('.modal-footer').empty();
        $('.modal-footer').append('<button type="button" class="btn btn-primary sellButton">Sell</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');

        body.on('change', '#currency', function () {
            var result = this.value * Number($('.sellSpan').text());
            accountBalance = account + result;
            $('.transactionValue').text(result.toFixed(2));
            $('.accountBalance').text(accountBalance.toFixed(2));
        });

        body.on('DOMSubtreeModified', '.sellSpan', function () {
            var result = $('#currency').val() * Number(this.innerText);
            accountBalance = account + result;
            $('.transactionValue').text(result.toFixed(2));
            $('.accountBalance').text(accountBalance.toFixed(2));
        });

        body.on('click', '.sellButton', function () {

            var sellRequest = $.ajax({
                url: "api/sell",
                cache: false,
                dataType: "json",
                type: "post",
                data: {time: time, currency:currency, amount: $('#currency').val()}
            });

            sellRequest.done(function (response) {
                location.reload();
            });

            sellRequest.fail(function (error) {
                var message = $.parseJSON(error.responseText);
                $('.modal-errors').empty();
                $('.modal-errors').text(message.message);
            })

        });
    });

    body.on('click', '.btn-buy', function (e) {

        var currency = this.parentNode.parentNode.childNodes[1].innerText;
        var buyValue = Number(this.parentNode.parentNode.childNodes[5].innerText);
        var account = Number($('#pln').text());
        var accountBalance = account - buyValue;
        accountBalance = accountBalance.toFixed(2);
        var maximumBuyCurrency = Math.floor(account/buyValue);

        $('#myModal').removeClass();
        $('#myModal').addClass('modal fade');
        $('#myModal').addClass(`buy ${currency}`);
        var modal = $('.modal-body');
        modal.empty();

        var buyInput = $(`<div class="form-group"><label for="currency">How much ${currency} you want to buy: </label>` +
            `<input type="number" value=1 name=${currency} min=1 max=${maximumBuyCurrency} class="form-control" id="currency"></div>`);

        var modalContent = $(`<div><p>Current ${currency} buy value: <span class="buySpan">${buyValue}</span> PLN</p><p>Transaction value: <span class="transactionValue">${buyValue}</span> PLN</p><p>Account balance after transaction <span class="accountBalance">${accountBalance}</span> PLN</p></div>`);
        modalContent.append(buyInput);
        $('.modal-title').empty();
        $('.modal-title').text(`Buy ${currency}`);
        modalContent.append('<p class="modal-errors" style="color: red"></p>');
        modal.append(modalContent);
        $('.modal-footer').empty();
        $('.modal-footer').append('<button type="button" class="btn btn-primary buyButton">Buy</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');

        body.on('change', '#currency', function () {
            var result = this.value * Number($('.buySpan').text());
            accountBalance = account - result;
            $('.transactionValue').text(result.toFixed(2));
            $('.accountBalance').text(accountBalance.toFixed(2));
        });

        body.on('DOMSubtreeModified', '.buySpan', function () {
            var result = $('#currency').val() * Number(this.innerText);
            accountBalance = account - result;
            $('.transactionValue').text(result.toFixed(2));
            $('.accountBalance').text(accountBalance.toFixed(2));
        });

        body.on('click', '.buyButton', function () {

            var sellRequest = $.ajax({
                url: "api/buy",
                cache: false,
                dataType: "json",
                type: "post",
                data: {time: time, currency:currency, amount: $('#currency').val()}
            });

            sellRequest.done(function (response) {
                location.reload();
            });

            sellRequest.fail(function (error) {
                var message = $.parseJSON(error.responseText);
                $('.modal-errors').empty();
                $('.modal-errors').text(message.message);
            })

        });

    });
});
