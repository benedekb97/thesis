@extends('layouts.main')

@section('title', 'Home')

@section('content')
    @isset($machine)
        <div class="row" style="margin-top:50px;">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Állapot
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" style="margin-bottom:0;">
                            <tr>
                                <td style="text-align:right;">Állapot</td>
                                <td id="machine-state">{{ $machine->getStatus() }}</td>
                            </tr>
                            <tr>
                                <td style="text-align:right;">Öltés</td>
                                <td>
                                    <span id="machine-current-stitch">{{ $machine->getCurrentStitch() ?? 0 }}</span>/{{ $machine->getDesign()->getStitchCount() }}</td>
                            </tr>
                            <tr>
                                <td style="text-align:right">Minta</td>
                                <td><span id="machine-current-design">{{ $machine->getCurrentDesign() ?? 0 }}</span>/<span id="machine-design-count">{{ $machine->getDesignCount() ?? 0 }}</span></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="progress" style="margin-bottom:0;">
                                        <div id="machine-stitches-progress"
                                             class="progress-bar progress-bar-striped {{ $machine->getProgressBarStyle() }}"
                                             style="width:{{ $machine->getStitchProgressBarPercentage() }}%; text-align:center;">
                                            {{ round($machine->getStitchProgressBarPercentage(), 2) }}%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="progress" style="margin-bottom:0; text-align:center;">
                                        <div id="machine-designs-finished-progress"
                                             class="progress-bar"
                                             style="width:{{ $finishedDesignsPercentage = $machine->getFinishedDesignsProgressBarPercentage() }}%; text-align:center;">
                                            {{ $machine->getCurrentDesign() - 1 }}
                                        </div>
                                        <div id="machine-designs-current-progress"
                                             class="progress-bar progress-bar-animated bg-warning progress-bar-striped"
                                             style="width: {{ $currentDesignsPercentage = $machine->getCurrentDesignProgressBarPercentage() }}%; text-align:center;">
                                            Aktuális
                                        </div>
                                        <div id="machine-designs-remaining"
                                             style="width: {{ 100 - $machine->getFinishedDesignsProgressBarPercentage() - $machine->getCurrentDesignProgressBarPercentage() }}%">
                                            @if(($remaining = $machine->getDesignCount() - $machine->getCurrentDesign()) !== 0){{ $remaining }}@endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="card" style="margin-top:20px; margin-bottom: 20px;">
                    <div class="card-header">Műveletek</div>
                    <div class="list-group">
                        <a class="list-group-item" href="{{ route('design.colors', ['design' => $machine->getDesign()->getId()]) }}">
                            Színek szerkesztése
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                @if(($design = $machine->getDesign()) === null)
                    show nothing
                @else
                    <div class="card">
                        <div class="card-header">
                            Rajzolat
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
                                    @if (array_key_exists($id === '' ? 0 : $id, $design->getColors() ?? []))
                                        <g id="color-{{ $id === '' ? 0 : $id }}" stroke="{{ $design->getColors()[$id === '' ? 0 : $id] }}">
                                    @else
                                        <g id="color-{{ $id === '' ? 0 : $id }}" stroke="rgb(0, 0, 0)">
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
                                        $currentPosition = \Illuminate\Support\Arr::first($design->getStitches())[0];
                                    @endphp
                                @endif

                                <g id="crosshair"
                                   style="stroke-width:2;
                                   @if ($design->hasBackgroundColor())
                                       stroke:rgba({{ 255 - $design->getBackgroundColor()['red'] }},{{ 255 - $design->getBackgroundColor()['green'] }},{{ 255 - $design->getBackgroundColor()['blue'] }},1);
                                   @else
                                       stroke:red;
                                   @endif"
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

@push('scripts')
    @isset($machine)
    <script>
        let currentStitch = {{ $machine->getCurrentStitch() }};
        @isset($design)
            let totalStitches = {{ $design->getStitchCount() }};
        @else
            let totalStitches = 0;
        @endisset

        window.Echo.private('machine-update')
            .listen('MachineStatusUpdateEvent', (e) => {
                $('#machine-state').html(e.status);
                $('#machine-current-design').html(e.currentDesign);
                $('#machine-current-stitch').html(e.currentStitch);
                $('#machine-design-count').html(e.designCount);
                $('#crosshair').attr('transform', `translate(${e.crosshairPosition.horizontal} ${e.crosshairPosition.vertical})`);
                let progressBar = $('#machine-stitches-progress');

                progressBar.css('width', `${e.stitchProgressBarPercentage}%`);
                progressBar.html(`${e.stitchProgressBarPercentage}%`);

                progressBar.attr('class', `progress-bar progress-bar-striped ${e.progressBarStyle}`);

                $('#machine-designs-current-progress').css('width', `${e.currentDesignProgressBarPercentage}%`);

                let finishedDesignsProgressBar = $('#machine-designs-finished-progress');

                finishedDesignsProgressBar.css('width', `${e.finishedDesignsProgressBarPercentage}%`);
                finishedDesignsProgressBar.html(e.currentDesign - 1);

                let remainingProgressBarPercentage = 100 - e.finishedDesignsProgressBarPercentage - e.currentDesignProgressBarPercentage;

                $('#machine-designs-remaining').html(
                    (e.currentDesign === e.designCount)
                        ? ''
                        : e.designCount - e.currentDesign
                ).css('width', `${remainingProgressBarPercentage}%`);


                if (currentStitch < e.currentStitch) {
                    for (let i = currentStitch; i <= e.currentStitch; i++) {
                        $(`#stitch-${i}`).css('opacity', 1);
                    }

                    for(let i = e.currentStitch; i < totalStitches; i++) {
                        $(`#stitch-${i}`).css('opacity', 0.2);
                    }
                }

                if (currentStitch > e.currentStitch) {
                    for (let i = currentStitch; i >= e.currentStitch; i--) {
                        $(`#stitch-${i}`).css('opacity', 0.2);
                    }
                }

                currentStitch = e.currentStitch;
            })
            .listen('MachineDesignUpdateEvent', (e) => {
                location.reload();
            })

    </script>
    @endisset
@endpush
