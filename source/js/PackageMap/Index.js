import PackageMap from './Container/PackageMap';

const domElements = document.getElementsByClassName('modularity-package-map');
const { translation, packageData } = modPackageMap;

const element = domElements[i];
ReactDOM.render(<PackageMap translation={translation} packageObject={packageData} />, element);
