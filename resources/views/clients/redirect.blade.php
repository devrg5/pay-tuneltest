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
                <form id="pay-form" name="pay-form" action="{{ session('clientUrl') }}" method="POST">
                </form>
                @if (session('message'))
                    <script type="text/javascript">
                        window.onload = function() {
                            setTimeout(function() {
                                document.forms['pay-form'].submit();
                            }, 3000);
                        };
                    </script>
                @else
                    <script type="text/javascript">
                        document.forms['pay-form'].submit();
                    </script>
                @endif
                
            </div>
        </div>
    </div>
@endsection