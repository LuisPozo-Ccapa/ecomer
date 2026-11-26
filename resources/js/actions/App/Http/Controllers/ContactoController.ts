import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ContactoController::store
 * @see app/Http/Controllers/ContactoController.php:18
 * @route '/api/contacto'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/contacto',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ContactoController::store
 * @see app/Http/Controllers/ContactoController.php:18
 * @route '/api/contacto'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ContactoController::store
 * @see app/Http/Controllers/ContactoController.php:18
 * @route '/api/contacto'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

    /**
* @see \App\Http\Controllers\ContactoController::store
 * @see app/Http/Controllers/ContactoController.php:18
 * @route '/api/contacto'
 */
    const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
        action: store.url(options),
        method: 'post',
    })

            /**
* @see \App\Http\Controllers\ContactoController::store
 * @see app/Http/Controllers/ContactoController.php:18
 * @route '/api/contacto'
 */
        storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
            action: store.url(options),
            method: 'post',
        })
    
    store.form = storeForm
const ContactoController = { store }

export default ContactoController