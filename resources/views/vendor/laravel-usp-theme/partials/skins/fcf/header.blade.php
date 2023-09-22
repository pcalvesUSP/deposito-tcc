@section('skin_styles')
@parent {{-- devemos incluir o conteúdo existente --}}
<style>

    /* #skin_header é o div pai */
    #skin_header {
        background-color: #5b305f;
        color: white;
    }

    #skin_header .container-fluid {
        display: block;
        font-size: 20px;
        height: 110px;
    }

    #skin_header .skin_logo img {
        height: 90px;
        margin: 10px;
    }

    #skin_header .skin_texto img {
        margin-top: 8px;
        height: 110px;
    }

</style>
@endsection

@section('skin_header')
<!-- container vai ocultar em mobile para ganhar espaço -->
<div class="container-fluid d-none d-sm-block">
    <div class="row" style="height:100px;">
        <div class="col-md-12">
            <a class="skin_logo" href="{{ env("APP_URL") }}">
                <img src="{{ asset('/vendor/laravel-usp-theme/fcf/images/LOGOFcf_nome.png') }}" alt="Logo da Faculdade de Ciências Farmacêuticas da USP" />
            </a>
            <span class="skin_texto" style="text-align:-webkit-right;font-size:30px;font-weight:bold;right:10%;position:relative;width:100%;height:10px;display:block;bottom:60%;">
                {{ env("APP_NAME") }}
                
            </span>
        </div>
    </div>
</div>
@endsection
