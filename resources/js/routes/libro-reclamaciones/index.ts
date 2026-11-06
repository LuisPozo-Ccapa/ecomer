import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import storeE72b01 from './store'
/**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/libro-de-reclamaciones'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/libro-de-reclamaciones',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/libro-de-reclamaciones'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/libro-de-reclamaciones'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/libro-de-reclamaciones'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\LibroReclamacionesController::store
 * @see app/Http/Controllers/LibroReclamacionesController.php:19
 * @route '/libro-de-reclamaciones'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
const libroReclamaciones = {
    store: Object.assign(store, storeE72b01),
}

export default libroReclamaciones