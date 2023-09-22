@component('mail::message')
# Prezad@ {{ $nome }}
{{ $textoMensagem }}
@component('mail::button', ['url' => config('app.url') ])
Acesse o Sistema
@endcomponent
<br/>
Este é um e-mail automático gerado pelo {{ config('app.name') }}<br/>
Para maiores informações entre em contato com a Comissão de TCC FF-USP.<br/>
<a href="mailto:ctcc.fcf@usp.br">ctcc.fcf@usp.br</a>
@endcomponent
