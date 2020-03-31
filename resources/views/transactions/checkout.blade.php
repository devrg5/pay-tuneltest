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
                            <img src="{{ asset('images/paybo/visa_175x175.png') }}" alt="Visa Logo" style="height:3.125rem;">
                            <img src="{{ asset('images/paybo/mc_175x175.png') }}" alt="Mastercard Logo" style="height:3.125rem;">
                            <img src="{{ asset('images/paybo/re_175x175.png') }}" alt="Red Enlace Logo" style="height:3.125rem;">
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
                        </div>
                        <form name="f1" id="f1" action="#" method="post" class="alignet-form-vpos2">
                            <input type="text" name="acquirerId" value="{{ $acquirerId }}" hidden>
                            <input type="text" name="idCommerce" value="{{ $idCommerce }}" hidden>
                            <input type="text" name="purchaseOperationNumber" value="{{ $purchaseOperationNumber }}" hidden>
                            <input type="text" name="purchaseAmount" value="{{ $purchaseAmount }}" hidden>
                            <input type="text" name="purchaseCurrencyCode" value="{{ $purchaseCurrencyCode }}" hidden>
                            <input type="text" name="language" value="{{ $language }}" hidden>
                            <input type="text" name="shippingFirstName" value="{{ $shippingFirstName }}" hidden>
                            <input type="text" name="shippingLastName" value="{{ $shippingLastName }}" hidden>
                            <input type="text" name="shippingEmail" value="{{ $shippingEmail }}" hidden>
                            <input type="text" name="shippingAddress" value="{{ $shippingAddress }}" hidden>
                            <input type="text" name="shippingZIP" value="{{ $shippingZIP }}" hidden>
                            <input type="text" name="shippingCity" value="{{ $shippingCity }}" hidden>
                            <input type="text" name="shippingState" value="{{ $shippingState }}" hidden>
                            <input type="text" name="shippingCountry" value="{{ $shippingCountry }}" hidden>
                            <input type="text" name="userCommerce" value="{{ $userCommerce }}" hidden>
                            <input type="text" name="userCodePayme" value="{{ $userCodePayme }}" hidden>
                            <input type="text" name="descriptionProducts" value="{{ $descriptionProducts }}" hidden>
                            <input type="text" name="programmingLanguage" value="{{ $programmingLanguage }}" hidden>
                            <input type="text" name="reserved1" value="{{ $reserved1 }}" hidden>
                            <input type="text" name="reserved2" value="{{ $reserved2 }}" hidden>
                            <input type="text" name="reserved3" value="{{ $reserved3 }}" hidden>
                            <input type="text" name="reserved4" value="{{ $reserved4 }}" hidden>
                            <input type="text" name="purchaseVerification" value="{{ $purchaseVerification }}" hidden>
                            <div class="row">
                                <div class="col">
                                    <button type="button" class="btn btn-outline-secondary btn-block" onclick="goBack()">{{ __('messages.cancel') }}</button>
                                </div>
                                <div class="col">
                                    @if (App::environment() === 'local')
                                        <button type="button" class="btn btn-primary btn-block" type="submit" onclick="javascript:AlignetVPOS2.openModal('https://integracion.alignetsac.com/')">{{ __('messages.pay') }}</button>
                                    @else
                                        <button type="button" class="btn btn-primary btn-block" type="submit" onclick="javascript:AlignetVPOS2.openModal('','2')">{{ __('messages.pay') }}</button>
                                    @endif
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
    <script type="text/javascript" src="https://integracion.alignetsac.com/VPOS2/js/modalcomercio.js" ></script>
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
    </script>
@endsection