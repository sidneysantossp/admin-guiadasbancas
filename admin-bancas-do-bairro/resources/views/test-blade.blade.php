<!DOCTYPE html>
<html>
<head>
    <title>Teste Blade</title>
</head>
<body>
    <h1>Teste de Renderização Blade</h1>
    
    <p>Teste básico: {{ "Hello World" }}</p>
    
    @php
        $test = "PHP Block funcionando";
        $langs = ['pt', 'en'];
    @endphp
    
    <p>Variável PHP: {{ $test }}</p>
    
    <ul>
    @foreach($langs as $lang)
        <li>Idioma: {{ $lang }}</li>
    @endforeach
    </ul>
    
    <p>Data atual: {{ now()->format('Y-m-d H:i:s') }}</p>
</body>
</html>
