@extends('layouts.main')

@section('title', 'Home')

@section('content')
    @isset($machine)
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Állapot</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <tr>
                                <td style="text-align:right;">Állapot</td>
                                <td>{{ $machine->getStatus() }}</td>
                            </tr>
                            <tr>
                                <td style="text-align:right;">Öltés</td>
                                <td>{{ $machine->getCurrentStitch() ?? 0 }}/{{ $machine->getDesign()->getStitchCount() }}</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Minta</td>
                                <td>{{ $machine->getCurrentDesign() ?? 0 }}/{{ $machine->getDesignCount() ?? 0 }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                @if(($design = $machine->getDesign()) === null)
                    show nothing
                @else
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Rajzolat</h5>
                        </div>
                        <div class="card-body">
                            <svg style="
                                margin:auto;
                                <?php $stitchCount = 0; ?>
                                @if (($background = $design->getBackgroundColor()) !== null)
                                    background-color:rgba({{ $background['red'] }}, {{ $background['green'] }}, {{ $background['blue'] }}, 0.8);
                                @else
                                    background-color:rgba(255, 255, 255, 1);
                                @endif
                                        "
                                 viewBox="0 0 {{ $design->getCanvasWidth() ?? 0 }} {{ $design->getCanvasHeight() ?? 0 }}"
                                 preserveAspectRatio="none"
                            >
                                @foreach($design->getStitches() as $id => $color)
                                    @if (array_key_exists($id, $design->getColors() ?? []))
                                        <g id="color-{{ $id }}" stroke="{{ $design->getColors()[$id] }}">
                                    @else
                                        <g id="color-{{ $id }}" stroke="rgb(0, 0, 0)">
                                    @endif

                                    @foreach ($color as $stitchId => $stitch)
                                        <line
                                            id="stitch-{{ $stitchCount }}"
                                            x1="{{ $stitch[0][0] + abs($design->getHorizontalOffset()) + 5 }}"
                                            x2="{{ $stitch[1][0] + abs($design->getHorizontalOffset()) + 5 }}"
                                            y1="{{ $stitch[0][1] + abs($design->getVerticalOffset()) + 5 }}"
                                            y2="{{ $stitch[1][1] + abs($design->getVerticalOffset()) + 5 }}"
                                            style="stroke-width:1.5;
                                                @if ($machine->getCurrentStitch() > $stitchCount)
                                                    opacity:1;
                                                @else
                                                    opacity:0.2;
                                                @endif
                                                "></line>
                                        <?php $stitchCount++; ?>

                                    @if ($machine->getCurrentStitch() === $stitchCount)
                                        @php $currentPosition = $stitch @endphp
                                    @endif
                                    @endforeach

                                    </g>
                                @endforeach

                                @if(!isset($currentPosition))
                                    @php
                                        $currentPosition = $design->getStitches()[1][0];
                                    @endphp
                                @endif

                                <g id="crosshair"
                                   style="stroke-width:2; stroke:red"
                                   transform="translate({{ $currentPosition[0][0] + abs($design->getHorizontalOffset()) + 5 }} {{ $currentPosition[0][1] + abs($design->getVerticalOffset()) + 5 }})">
                                    <line x1="0" x2="0" y1="3" y2="13"></line>
                                    <line x1="0" x2="0" y1="-3" y2="-13"></line>
                                    <line x1="3" x2="13" y1="0" y2="0"></line>
                                    <line x1="-3" x2="-13" y1="0" y2="0"></line>
                                </g>
                            </svg>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endisset
@endsection
