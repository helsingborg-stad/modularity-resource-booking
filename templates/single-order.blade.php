<div class="grid grid--columns u-pt-4@md u-pt-4@lg u-pt-4@xl">
    
    @if (isset($notice) && $notice)
    <div class="grid-xs-12">
        <div class="notice warning">
            <i class="pricon pricon-notice-warning"></i> {!!$notice!!}
        </div>
    </div>
    @endif

    <div class="grid-xs-12 grid-md-6">

        <div class="grid grid--columns">

        {{-- Summary --}}
        <div class="grid-xs-12">
            @if (is_array($summary['items']) && !empty($summary['items']))
                <table class="table table--plain">
                    <tbody>
                        @foreach($summary['items'] as $article)
                            <tr>
                                <td class="u-pb-3">
                                    <b>{{$article['name']}}</b>
                                    <br>
                                    <small><b>{{$article['week']}}</b></small>
                                    <br>
                                    <small>Startdatum: {{$article['start']}}</small>
                                    <br>
                                    <small>Slutdatum: {{$article['stop']}}</small>
                                </td  class="u-pb-3">
                                <td class="text-right">{{$article['price']}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="u-pt-4"><b>Totalt:</b></td>
                            <td class="u-pt-4 text-right">
                                <b>{{$summary['totalPrice']}}</b>
                                <br>
                                <small>ink. moms</small>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
        {{-- // Summary--}}
        </div>
    </div>
    
    <div class="grid-xs-12 grid-md-6 u-mb-4">
        <div class="c-card">
            <div class="c-card__header">
                    <h4>Orderdetaljer</h4>
            </div>
            {{-- Details --}}
            <div class="c-card__body u-pb-2">
                @if (is_array($orderDetails) && !empty($orderDetails))
                    
                    @foreach ($orderDetails as $item)
                        <div>
                            <p class="u-mb-1"><small>{{$item['label']}}: </small>
                            <b><br>{{$item['value']}}</b>
                            </p>
                        </div>
                    @endforeach
                @endif
            </div>
            {{-- // Details --}}


        </div>

    </div>
    {{-- Uploads --}}
    @if (isset($uploadFormDataAttribute) && !empty($uploadFormDataAttribute))
    <div class="grid-xs-12 grid-md-12 u-mb-4 u-order-4">
            <div class="c-card">
                    <div class="c-card__header">
                        <h4>Ladda upp annonsmaterial</h4>
                    </div>

                <div class="c-card__body">
                    <div class="modularity-resource-upload-form" data-upload-form='{{$uploadFormDataAttribute}}'>
                        <div class="gutter gutter-xl">
                            <div class="loading">
                                <div></div>
                                <div></div>
                                <div></div>
                                <div></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
    </div>
    @else
    <hr style="height: 1px;">
    @endif
    {{-- // Uploads --}}
</div>



