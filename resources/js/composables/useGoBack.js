import { usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

export function useGoBack() {
    const page = usePage()

    const goBack = () => {
        const previousUrl = page.props.previousUrl
        if (previousUrl) {
            router.visit(previousUrl)
        } else {
            window.history.back()
        }
    }

    return { goBack }
}

// En cualquier componente Vue
// import { useGoBack } from '@/composables/useGoBack'

// data() {
//     return {
//         goBack: useGoBack().goBack
//     }
// },
