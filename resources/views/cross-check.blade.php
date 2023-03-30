<H1>RCL: Year {{ strtoupper($year) }}</H1>

@foreach($weeks as $week) 

    <div>
        <h2>[{{ $week->id }}] {{ $week->title }}</h2>

        @foreach($week->readings($year)->get() as $reading)

        <div tabindex="0">{{ $reading->reference }}</div>

        @endforeach

    </div>


@endforeach