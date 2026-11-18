import ContactoController from './ContactoController'
import Api from './Api'
import DeployWebhookController from './DeployWebhookController'
import SitemapController from './SitemapController'
import PlantillasController from './PlantillasController'
import SitePageController from './SitePageController'
import LibroReclamacionesController from './LibroReclamacionesController'
const Controllers = {
    ContactoController: Object.assign(ContactoController, ContactoController),
Api: Object.assign(Api, Api),
DeployWebhookController: Object.assign(DeployWebhookController, DeployWebhookController),
SitemapController: Object.assign(SitemapController, SitemapController),
PlantillasController: Object.assign(PlantillasController, PlantillasController),
SitePageController: Object.assign(SitePageController, SitePageController),
LibroReclamacionesController: Object.assign(LibroReclamacionesController, LibroReclamacionesController),
}

export default Controllers