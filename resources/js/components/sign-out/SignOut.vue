<template>
    <a 
    @click="signOut()"
    class="button-ajax menu-link px-5">
        Sign Out
    </a>
</template>
<script>
import ResponseMixins from "Mixins/response.js";
export default {
    name: "sign-out",

    props: {
        signOutUrl: {
            type: String,
            default: ""
        }
    },

    mixins: [ResponseMixins],

    methods: {
        signOut() {
            Swal.fire({
                title: "Signing Out?",
                html: "Are you sure you want to sign out?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "Sign Out"
            }).then(async (result) => {
                if (result.isConfirmed) {
                    axios.post(`${this.signOutUrl}?ajax=true`)
                        .then((res) => {
                            KTThemeMode.setMode("light");
                            window.location.href = "/login";
                        }).catch((err) => {
                            this.parseError(err, {});
                        });
                }
            });            
        }
    }
}
</script>