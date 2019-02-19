<div class="grid grid--columns">
    
    {{-- Details --}}
    <div class="grid-xs-12">
        @if (is_array($orderDetails) && !empty($orderDetails))
            <h4 class="u-mb-3">Orderdetaljer</h4>
            @foreach ($orderDetails as $item)
                <div>
                    <b>{{$item['label']}}: </b>
                    <span>{{$item['value']}}</span>
                </div>
            @endforeach
        @endif
    </div>
    {{-- // Details --}}
    
    {{-- Uploads --}}
    @if (isset($uploadFormDataAttribute) && !empty($uploadFormDataAttribute))
        <div class="grid-xs-12 u-mb-4">
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
    @endif
    {{-- // Uploads --}}

    {{-- Summary --}}
    <div class="grid-xs-12">
        @if (is_array($summary['items']) && !empty($summary['items']))
            <h4>Summering</h4>
            <table class="table table--plain">
                <tbody>
                    @foreach($summary['items'] as $article)
                        <tr>
                            <td class="u-pb-2">
                                {{$article['name']}}
                                <br>
                                <small><b>Startdatum: </b>{{$article['start']}}</small>
                                <br>
                                <small><b>Slutdatum: </b>{{$article['stop']}}</small>
                            </td  class="u-pb-2">
                            <td class="text-right">{{$article['price']}}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td class="u-pt-2"><b>Totalt:</b></td>
                        <td class="u-pt-2 text-right">
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



