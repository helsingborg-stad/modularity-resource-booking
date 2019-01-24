const mapStyles = {
    map: {
        position: 'absolute',
        width: '100%',
        height: '100%',
    },
};

export class CurrentLocation extends React.Component {
    constructor(props) {
        super(props);
        const { initialCenter } = this.props;
        const { lat, lng } = initialCenter;
        this.state = {
            currentLocation: {
                lat: lat,
                lng: lng,
            },
        };
    }

    componentDidUpdate(prevProps, prevState) {
        const { google } = this.props;
        const { currentLocation } = this.state;
        if (prevProps.google !== google) {
            this.loadMap();
        }
        if (prevState.currentLocation !== currentLocation) {
            this.recenterMap();
        }
    }

    recenterMap() {
        const { map } = this.map;
        const { currentLocation } = this.state;

        const { google } = this.props;
        const { maps } = google.maps;

        if (map) {
            const center = new maps.LatLng(currentLocation.lat, currentLocation.lng);
            map.panTo(center);
        }
    }

    componentDidMount() {
        const { centerAroundCurrentLocation } = this.props;
        if (centerAroundCurrentLocation) {
            if (navigator && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(pos => {
                    const { coords } = pos.coords;
                    this.setState({
                        currentLocation: {
                            lat: coords.latitude,
                            lng: coords.longitude,
                        },
                    });
                });
            }
        }
        this.loadMap();
    }

    loadMap() {
        const { google } = this.props;
        if (this.props && google) {
            // checks if google is available
            const { google } = this.props;
            const { maps } = google.maps;

            const mapRef = this.refs.map;

            // reference to the actual DOM element
            const node = ReactDOM.findDOMNode(mapRef);

            const { zoom } = this.props;
            const { currentLocation } = this.state;
            const { lat, lng } = currentLocation;
            const center = new maps.LatLng(lat, lng);
            const mapConfig = Object.assign(
                {},
                {
                    center: center,
                    zoom: zoom,
                }
            );

            // maps.Map() is constructor that instantiates the map
            this.map = new maps.Map(node, mapConfig);
        }
    }

    renderChildren() {
        const { children } = this.props;

        if (!children) return;

        return React.Children.map(children, c => {
            if (!c) return;
            const { google } = this.props;
            const { currentLocation } = this.state;
            return React.cloneElement(c, {
                map: this.map,
                google: google,
                mapCenter: currentLocation,
            });
        });
    }

    render() {
        const style = Object.assign({}, mapStyles.map);
        return (
            <div>
                <div style={style} ref="map">
                    Loading map...
                </div>
                {this.renderChildren()}
            </div>
        );
    }
}
export default CurrentLocation;

CurrentLocation.defaultProps = {
    zoom: 14,
    initialCenter: {
        lat: -1.2884,
        lng: 36.8233,
    },
    centerAroundCurrentLocation: false,
    visible: true,
};
