@extends('layouts.app')

@section('content')

@auth
 <h1> Bem vindo ao Sistema de Depósito de TCC. Use o menu para navegação.</h1>
@endauth

@guest
    <div class="container-fluid text-center">
        <div class="row">
            <div class="col">
                <button id="login_USP" class="btn btn-primary">Tenho vínculo com a FCF</button>
            </div>
        </div>
        <div class="row">
            <div class="col">
                &nbsp;
            </div>
        </div>
        <div class="row">
            <div class="col">
                <button id="login_externo" class="btn btn-primary">Não tenho vínculo com a FCF</button>
            </div>
        </div>

        <script>
            $(document).ready(function() {

                $("#login_USP").click(function() {
                    window.location.assign('/login');
                });

                $('#login_externo').click(function() {
                    window.location.assign('/loginExterno');
                });


            });
        </script>
@endguest

@endsection