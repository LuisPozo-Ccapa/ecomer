import Api from './Api'
import SitemapController from './SitemapController'
import PlantillasController from './PlantillasController'
import SitePageController from './SitePageController'
import LibroReclamacionesController from './LibroReclamacionesController'
const Controllers = {
    Api: Object.assign(Api, Api),
SitemapController: Object.assign(SitemapController, SitemapController),
PlantillasController: Object.assign(PlantillasController, PlantillasController),
SitePageController: Object.assign(SitePageController, SitePageController),
LibroReclamacionesController: Object.assign(LibroReclamacionesController, LibroReclamacionesController),
}

export default Controllers