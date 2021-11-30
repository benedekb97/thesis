@extends('layouts.main')

@section('title', 'Edit design')

@section('content')
    <div class="row" style="margin-top:50px;">
        <div class="col-md-4">
            <form action="{{ route('design.colors', ['design' => $design->getId()]) }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">Szinek</div>
                    <div class="card-body">
                        @foreach ($design->getStitches() as $id => $color)
                            <div class="input-group" style="margin-bottom:5px;">
                                <label class="input-group-text" for="color-{{ $id === '' ? 0 : $id }}">{{ $id === '' ? 1 : $id + 1 }}. szín</label>
                                <input @if ($design->hasColors() && $design->hasColor($id === '' ? 0 : $id)) value="{{ $design->getColors()[$id === '' ? 0 : $id] }}" @endif class="form-control-color" type="color" name="color-{{ $id === '' ? 0 : $id }}" id="color-{{ $id === '' ? 0 : $id }}">
                            </div>
                        @endforeach
                    </div>
                    <div class="card-header" style="border-top: 1px solid rgba(0, 0, 0, 0.15)">Háttérszín</div>
                    <div class="card-body">
                        <div class="input-group">
                            <label for="background" class="input-group-text">Háttérszín</label>
                            <input class="form-control-color" type="color" name="background" id="background" value="{{ $design->getHexBackgroundColor() }}"/>
                        </div>
                    </div>
                </div>
                <div style="margin-top:20px">
                    <button class="btn btn-primary">Mentés</button>
                </div>
            </form>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Rajzolat</div>
                <div class="card-body">
                    <svg style="@if ($design->hasBackgroundColor()) background:rgba(
                        {{ $design->getBackgroundColor()['red'] }},
                        {{ $design->getBackgroundColor()['green'] }},
                        {{ $design->getBackgroundColor()['blue'] }},
                        0.75
                        ); @endif margin:auto;"
                         id="image"
                         viewBox="0 0 {{ $design->getCanvasWidth() }} {{ $design->getCanvasHeight() }}"
                         preserveAspectRatio="none">
                        @foreach ($design->getStitches() as $id => $color)
                            @if ($design->hasColors() && $design->hasColor($id === '' ? 0 : $id))
                                <g id="svg-color-{{ $id === '' ? 0 : $id }}" style="stroke:{{ $design->getColors()[$id === '' ? 0 : $id] }}">
                            @else
                                <g id="svg-color-{{ $id === '' ? 0 : $id }}" style="stroke:black;">
                            @endif
                            @foreach ($color as $stitch)
                                <line x1="{{ $stitch[0][0] + abs($design->getHorizontalOffset()) + 5 }}"
                                      x2="{{ $stitch[1][0] + abs($design->getHorizontalOffset()) + 5 }}"
                                      y1="{{ $stitch[0][1] + abs($design->getVerticalOffset()) + 5 }}"
                                      y2="{{ $stitch[1][1] + abs($design->getVerticalOffset()) + 5 }}"
                                      style="stroke-width:2;"></line>
                            @endforeach

                            </g>
                        @endforeach
                    </svg>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let colorCount = {{ count($design->getStitches()) }};

        $('#background').change(
            function (e) {
                let svgBackground = $('#image');
                let rgb = hexToRgb(e.target.value);

                svgBackground.css('background', `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, 0.75)`);
            }
        )

        @foreach ($design->getStitches() as $id => $color)
            console.log('Color change' + {{ $id === '' ? 0 : $id }});

            $('#color-{{ $id === '' ? 0 : $id }}').change(
                function (e) {
                    $('#svg-color-{{ $id == '' ? 0 : $id }}').css('stroke', e.target.value);
                }
            );

            $('#color-{{ $id === '' ? 0 : $id }}').hover(
                function() {
                    console.log('Hover' + {{ $id === '' ? 0 : $id }});

                    for (let i = 0; i < colorCount; i++) {
                        if (i !== {{ $id === '' ? 0 : $id }}) {
                            $('#svg-color-' + i).css('opacity', 0.5);
                        }
                    }
                }
            );

            $('#color-{{ $id === '' ? 0 : $id }}').mouseleave(
                function() {
                    console.log('Mouse leave' + {{ $id === '' ? 0 : $id }});

                    for (let i = 0; i < colorCount; i++) {
                        if (i !== {{ $id === '' ? 0 : $id }}) {
                            $('#svg-color-' + i).css('opacity', 1);
                        }
                    }
                }
            );
        @endforeach

        function hexToRgb(hex) {
            let result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? {
                r: parseInt(result[1], 16),
                g: parseInt(result[2], 16),
                b: parseInt(result[3], 16)
            } : null;
        }
    </script>
@endpush
