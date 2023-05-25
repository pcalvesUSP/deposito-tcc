@extends('layouts.app')

@section('content')

<h1 style="text-align:center;">Lista de Monografias @if ($userLogado == "Orientador") para avaliação @endif </h1>
@foreach ($dadosMonografias as $objMonografia) {
    {{ $objMonografia->titulo }}
@endforeach



{{ $dadosMonografias->links() }}
<script>
    $( document ).ready(function(){

        $("#filtro").keyup(function() {
            if ($(this).val().length > 3) {
                document.getElementById("filtrarMonografia").submit();
            }
        });
    });

</script>

@endsection