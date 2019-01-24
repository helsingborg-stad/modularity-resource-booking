import { GoogleApiWrapper, InfoWindow, Marker } from 'google-maps-react';
import { CurrentLocation } from '../Components/Map.js';
import { mapData } from '../../Api/mapData.js';

export class MapContainer extends React.Component {
    state = {
        showingInfoWindow: false,
        activeMarker: {},
        selectedPlace: {},
    };

    constructor(props) {
        super(props);

        this.setState({
            id: null,
            trans: null,
        });
    }

    componentDidMount() {
        const { packageId, translation } = props.packageObject;
        this.setState({
            id: packageId,
            trans: translation,
        });
    }

    onMarkerClick = (props, marker, e) =>
        this.setState({
            selectedPlace: props,
            activeMarker: marker,
            showingInfoWindow: true,
        });

    onClose = props => {
        const { showingInfoWindow } = this.state;
        if (showingInfoWindow) {
            this.setState({
                showingInfoWindow: false,
                activeMarker: null,
            });
        }
    };

    render() {
        const { id } = this.state;
        const apiData = this.mapData(id);
        console.log(apiData);
        const { activeMarker, showingInfoWindow, selectedPlace } = this.state;
        const { google } = this.props;
        return (
            <CurrentLocation centerAroundCurrentLocation google={google}>
                <Marker onClick={this.onMarkerClick} name={'current location'} />
                <InfoWindow
                    marker={activeMarker}
                    visible={showingInfoWindow}
                    onClose={this.onClose}
                >
                    <div>
                        <h4>{selectedPlace.name}</h4>
                    </div>
                </InfoWindow>
            </CurrentLocation>
        );
    }
}

export default GoogleApiWrapper({
    apiKey: 'AIzaSyDTzuHi0a-nqoXymo79QfQewSRhXf2EPik',
})(MapContainer);
