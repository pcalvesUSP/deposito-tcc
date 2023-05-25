<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Declaração Aluno Monografia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">    
    <style>
       body {
           margin: 7%;
           font-family: Arial;
       } 
       p{
           margin-bottom:1.5cm;
           text-align: justify;
           font-size: 22px;           
       }
       img{
        display: block;
        width: 4cm;
        height: auto;

       }
       h2{
           text-align: center;
           color: grey;
           width: fit-content;
           display: inline-block;
           top: -130px;
           position: relative;
           left: 280px;
       }
       h3{
        text-align: center;
        font-size:30px;
       }
       .assinatura img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 70%;
       }
       @media print {
			@page { margin: 0; }
  			body { margin: 1.6cm; }
		}
    </style>
</head>
<body>
@if (isset($msgErro)) 
    <h1>{{ $msgErro }}
@else
    <!--Cabeçalho-->
    <div class="container">
        <div class="row justify-content-start">
            <div class="col-3">
               <img src="http://www.ee.usp.br/novamonografia/application/views/certificado/img/logo.jpg"><br><br>
            </div>  
            <div class="col-6">
                <h2>Escola de Enfermagem<br> Universidade de São Paulo</h2>               
            </div>
            <div class="col-3"></div>
            
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-start">
            <div class="col-1"></div>  
            <div class="col-10">
                <h3>Declaração</h3><br><br>
            </div>
            <div class="col-1"></div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-start">
          <div class="col-1"></div>  
          <div class="col-10">
            <p>Certifico que <b>{{ $nomeModerador }}</b>, participou como moderador(a) na <b>{{ $mostra }}</b>
            Mostra de Trabalhos de Conclusão de Curso de Bacharelado da Escola de Enfermagem da Universidade de São Paulo,
            realizada em {{ $mesMostra }} de <b>{{ $ano }}</b>.
            </p><br><br>
          </div>
          <div class="col-1"></div>
        </div>
    </div>
    <br><br>

    <!--Assinatura de coordenação-->
    <div class="container assinatura">
            <div class="row justify-content-start">
              <div class="col-1"></div>  
              <div class="col-10">
                <p style="text-align:center;font-size=18"><img src="{{ $imgAssinatura }}" style="width:300px;"><br/>
                Prof.(ª) Dr.(ª) {{ $nomeCoordenador }}<br/>
                Coordenador(a)
                </p>
                <br><br>
                <!--
                <p>Prof.ª Dr.ª {{ $nomeCoordenador }}<br>Coordenadora</p><br>                
                <p>Comissão de Coordenação de Curso do Bacharelado</p>
                -->
              </div>
              <div class="col-1"></div>
            </div>
        </div>

    <!-- Script para imprimir assim acaba de carregar o HTML  -->
    <script>
        window.print();
    </script>
@endif
</body>
</html>