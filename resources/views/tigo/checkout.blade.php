@extends('master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="card mx-auto info-card">
                    <div class="card-header text-center">
                        <h5 class="card-title d-inline">{{ __('messages.welcome', ['name' => $shippingFirstName]) }} </h5><img src="{{ asset('images/paybo/logo.png') }}" alt="PayBo Logo" style="vertical-align: bottom; height:1.5625rem;">
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ __('messages.firstSentence') }}<br/>{{ __('messages.secondSentence') }}<br/>{{ __('messages.thirdSentence') }}</p>
                        <div class="d-flex flex-row justify-content-around">
                            <img src="{{ asset('images/paybo/tigo_money.jpg') }}" alt="Tigo Money Logo" style="height:3.125rem;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mx-auto personal-info-card">
                    <div class="card-header">
                        <h5 class="card-title d-inline">{{ __('messages.details') }}</h5>
                        <img src="{{ asset('images/clients-logo/'.$clientImage) }}" alt="Totto Logo" class="float-right" style="height:1.3125rem;">
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col">
                                <div>
                                    <p>
                                        <strong>{{ __('messages.nameLastName') }}</strong><br>
                                        {{ $shippingFirstName }} {{ $shippingLastName }}
                                    </p>
                                    <p>
                                        <strong>{{ __('messages.amountToPay') }}</strong><br>
                                        @if ($purchaseCurrencyCode === '068')
                                            Bs {{ $purchaseAmount/100 }}
                                        @elseif ($purchaseCurrencyCode === '840')
                                            USD {{ $purchaseAmount/100 }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col">
                                <div>
                                    <p>
                                        <strong>Ingrese su numero de celular Tigo Money</strong><br>
                                        <input type="text" name="inputPhone" id="inputPhoneId" value="75146006">
                                    </p>
                                </div>
                            </div>
                        </div>
                        <form name="f1" id="f1" action="#" method="post">
                            <input type="text" name="purchaseOperationNumber" id="purchaseOperationNumberId" value="{{ $purchaseOperationNumber }}" hidden>
                            <input type="text" name="purchaseAmount" id="purchaseAmountId" value="{{ $purchaseAmount }}" hidden>
                            <input type="text" name="purchaseCurrencyCode" id="purchaseCurrencyCodeId" value="{{ $purchaseCurrencyCode }}" hidden>
                            <input type="text" name="language" id="languageId" value="{{ $language }}" hidden>
                            <input type="text" name="shippingFirstName" id="shippingFirstNameId" value="{{ $shippingFirstName }}" hidden>
                            <input type="text" name="shippingLastName" id="shippingLastNameId" value="{{ $shippingLastName }}" hidden>
                            <input type="text" name="shippingEmail" id="shippingEmailId" value="{{ $shippingEmail }}" hidden>
                            <input type="text" name="shippingAddress" id="shippingAddressId" value="{{ $shippingAddress }}" hidden>
                            <input type="text" name="shippingZIP" id="shippingZIPId" value="{{ $shippingZIP }}" hidden>
                            <input type="text" name="shippingCity" id="shippingCityId" value="{{ $shippingCity }}" hidden>
                            <input type="text" name="shippingState" id="shippingStateId" value="{{ $shippingState }}" hidden>
                            <input type="text" name="shippingCountry" id="shippingCountryId" value="{{ $shippingCountry }}" hidden>
                            <input type="text" name="descriptionProducts" id="descriptionProductsId" value="{{ $descriptionProducts }}" hidden>
                            <input type="text" name="reserved1" id="reserved1Id" value="{{ $reserved1 }}" hidden>
                            <input type="text" name="reserved2" id="reserved2Id" value="{{ $reserved2 }}" hidden>
                            <input type="text" name="reserved3" id="reserved3Id" value="{{ $reserved3 }}" hidden>
                            <input type="text" name="purchaseVerification" id="purchaseVerificationId" value="{{ $purchaseVerification }}" hidden>
                            <input type="text" name="verificationCheck" id="verificationCheckId" value="{{ $verificationCheck}}" hidden>
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn btn-outline-secondary btn-block" onclick="goBack()">{{ __('messages.cancel') }}</button>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-primary btn-block" type="submit" onclick="payTigo()">{{ __('messages.pay') }}</button>
                                </div>
                                <div class="col">
                                    <button type="button" class="btn btn-primary btn-block" type="submit" onclick="checkStatus()">Check</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <footer style="">
                    <p>{!! __('messages.information', ['name' => 'Pay<sup>Bo</sup>']) !!} <a href="http://www.pay.com.bo/" target="_blank">www.pay.com.bo</a></p>
                </footer>
            </div>
        </div>
    </div>
    <script>
        function goBack() {
            const form = document.createElement('form');
            form.method = 'post';
            form.action = '/transaction/cancel';

            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'reserved3';
            hiddenField.value = "{{ $reserved3 }}";
            form.appendChild(hiddenField);

            const hiddenField1 = document.createElement('input');
            hiddenField1.type = 'hidden';
            hiddenField1.name = 'reserved1';
            hiddenField1.value = "{{ $reserved1 }}";
            form.appendChild(hiddenField1);

            document.body.appendChild(form);
            form.submit();
        }

        function payTigo(){
            let data = {
                phone: document.getElementById('inputPhoneId').value,
                reserved1: document.getElementById('reserved1Id').value,
                reserved2: document.getElementById('reserved2Id').value,
                reserved3: document.getElementById('reserved3Id').value,
                verification: document.getElementById('purchaseVerificationId').value
            };

			fetch('http://pay-master.test/transaction/tigo/pay', {
                  method: 'POST',
                  body: JSON.stringify(data),
				  headers:{
				    'Content-Type': 'application/json'
				  }
			}).then(response => response.json())
			.then(data => {
				console.log(data);
			}).catch(error => console.error('Error:', error));
        }
        function checkStatus(){
            let data = {
                purchaseOperationNumber: document.getElementById('purchaseOperationNumberId').value,
                reserved1: document.getElementById('reserved1Id').value,
                reserved3: document.getElementById('reserved3Id').value,
                verificationCheck: document.getElementById('verificationCheckId').value
            };

			fetch('http://pay-master.test/transaction/tigo/check', {
                  method: 'POST',
                  body: JSON.stringify(data),
				  headers:{
				    'Content-Type': 'application/json'
				  }
			}).then(response => response.json())
			.then(data => {
				console.log(data);
			}).catch(error => console.error('Error:', error));
        }
    </script>
@endsection
