@extends('master')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                @if (session('message'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('message') }}
                    </div>
                @endif
                <form id="pay-form" name="tunel-form" action="http://pay.test/api/payment/cybersource" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="inputName">Nombre</label>
                        <input type="text" class="form-control" id="inputName" name="inputName" value="Gonzalo">
                    </div>
                    <div class="form-group">
                        <label for="inputLastName">Apellido</label>
                        <input type="text" class="form-control" id="inputLastName" name="inputLastName" value="Ramirez">
                    </div>
                    <div class="form-group">
                        <label for="inputEmail">Email</label>
                        <input type="email" class="form-control" id="inputEmail" name="inputEmail" value="aaa@gmail.com">
                    </div>
                    <div class="form-group">
                        <label for="inputDescription">Descripcion</label>
                        <input type="text" class="form-control" id="inputDescription" name="inputDescription" value="2 productos (Televisor y Heladera)">
                    </div>
                    <div class="form-group">
                        <label for="inputAmount">Monto</label>
                        <input type="text" class="form-control" id="inputAmount" name="inputAmount" value="75">
                    </div>
                    <input type="text" name="inputClientIdentifier" value="c7c4c0f4-904a-4c2d-bfd6-92c1de5f5cc3" hidden>
                    <input type="text" name="orderId" value="50" hidden>
                    <input type="text" name="inputCurrency" value="BOB" hidden>
                    <input type="text" name="inputAddress" value="B/57 viviendas N163" hidden>
                    <input type="text" name="inputZip" value="000" hidden>
                    <input type="text" name="inputCity" value="Tarija" hidden>
                    <input type="text" name="inputState" value="Tarija" hidden>
                    <input type="text" name="inputCountry" value="BO" hidden>
                    <input type="text" name="inputLang" value="SP" hidden>
                    @if (App::environment() === 'local')
                        <input type="text" name="inputEnv" value="1" hidden>
                        <input type="text" name="verificationNumber" value="04ea2cc5f9fec1986cbb8d706f69671aea1ea31ac254035a1cc5d63ea4413a521fad62dabf79b9193c04b822738dc2a55958c86ae39a7b3f32cbe38c3643c833" hidden>
                    @else
                        <input type="text" name="inputEnv" value="0" hidden>
                        <input type="text" name="verificationNumber" value="a0f1190165099aad4711689276650e3d5de603b60a4f4f5d4a776f0cdd27040d8b096ce57fcf16f845794a601769b8d553fe01aa213ffcaaf724ecb380a26a3a" hidden>
                    @endif
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
@endsection
