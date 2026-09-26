import ProductController from './ProductController'
import CustomerController from './CustomerController'
import Settings from './Settings'
const Controllers = {
    ProductController: Object.assign(ProductController, ProductController),
CustomerController: Object.assign(CustomerController, CustomerController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers