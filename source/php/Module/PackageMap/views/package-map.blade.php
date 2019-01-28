@if($data['apiKey'])

<div id="mod-rb-google-map"></div>

<script>

    let jsonData = {!!json_encode($data)!!}

    function initGoogleMap() {

        let bounds = new google.maps.LatLngBounds();
        let map, marker, image, info = [];

        if (isUndefined(parseFloat(jsonData.lat)) && isUndefined(parseFloat(jsonData.lng))) {
            map = new google.maps.Map(document.getElementById('mod-rb-google-map'), {
                zoom: 12,
                center: {lat: parseFloat(jsonData.lat), lng: parseFloat(jsonData.lng)}
            });
        }

        if (isUndefined(jsonData.getPackageData)) {
            for (let item in jsonData.getPackageData) {

                info[item] += '<div class="grid">';
                info[item] += (isUndefined(jsonData.getPackageData[item].title)) ? '<h3>' + jsonData.getPackageData[item].title + '</h3>' : '';
                info[item] += (isUndefined(jsonData.getPackageData[item].content)) ? '<p>' + jsonData.getPackageData[item].content + '</p>' : '';

                for (let prodItem in jsonData.getPackageData[item].productSpec.value) {
                    info[item] += '<div class="grid-md-6">';
                    info[item] += '<br />';

                    info[item] += (isUndefined(jsonData.getPackageData[item].productSpec.value[prodItem].media_name)) ?
                        '<p><span>' + jsonData.translation.medianame + ': </span>' +
                        jsonData.getPackageData[item].productSpec.value[prodItem].media_name + '<p/>'
                        : '';
                    info[item] += (isUndefined(jsonData.getPackageData[item].productSpec.value[prodItem].media_type)) ? '<p><span>'
                        + jsonData.translation.mediatype + ' : </span>' +
                        jsonData.getPackageData[item].productSpec.value[prodItem].media_type + '<p/>'
                        : '';

                    info[item] += (isUndefined(jsonData.getPackageData[item].productSpec.value[prodItem].maxiumum_filesize)) ?
                        '<p><span>' + jsonData.translation.maxfilesize + ' : </span>' +
                        jsonData.getPackageData[item].productSpec.value[prodItem].maxiumum_filesize + 'MB<p/>'
                        : '';

                    info[item] += (isUndefined(jsonData.getPackageData[item].productSpec.value[prodItem].image_width)) ?
                        '<p><span>' + jsonData.translation.size + ' : </span>' +
                        jsonData.getPackageData[item].productSpec.value[prodItem].image_width + 'px x ' +
                        jsonData.getPackageData[item].productSpec.value[prodItem].image_height + 'px<p/>'
                        : '';

                    info[item] += '</div>';
                }

                info[item] += '</div>';

                info[item] += (isUndefined(jsonData.getPackageData[item].location.value.address)) ? '<br /> <p><i class="pricon pricon-helsingborg"></i> ' +
                    jsonData.getPackageData[item].location.value.address + ' </span></p>' : '';
                info[item] += '<br />';

                if (isUndefined(jsonData.getPackageData[item].location.value.lat) && isUndefined(jsonData.getPackageData[item].location.value.lng)) {

                    image = {
                        url: jsonData.url + '/dist/img/recource-marker.png',
                        size: new google.maps.Size(25, 39),
                        origin: new google.maps.Point(0, 0),
                        anchor: new google.maps.Point(0, 32)
                    };

                    marker = new google.maps.Marker({
                        position: new google.maps.LatLng(jsonData.getPackageData[item].location.value.lat,
                            jsonData.getPackageData[item].location.value.lng),
                        name: jsonData.getPackageData[item].title,
                        map: map,
                        icon: image,
                    });
                }

                (isUndefined(bounds) && isUndefined(marker.position) ) ? bounds.extend(marker.position) : '';

                if (isUndefined(item) && isUndefined(marker)) {
                    google.maps.event.addListener(marker, 'click', (function (marker, item) {

                        return function () {
                            const infoWindow = new google.maps.InfoWindow();
                            infoWindow.setContent(info[item]);
                            infoWindow.open(map, marker);
                        }


                    })(marker, item));
                }

            }
        }

        (map) ? map.fitBounds(bounds) : '';
    };

    function isUndefined(param){
        return (typeof param !== 'undefined') ? true : false;
    }
</script>

<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $data['apiKey']; ?>&callback=initGoogleMap"></script>
@endif

