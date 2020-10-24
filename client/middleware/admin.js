export default async function ({ $auth, redirect, $axios }) {
    // console.log($axios)
    try {
        let response = await $axios.get("/api/admin/is-admin")
    } catch (e) {
        return redirect("/updates")
    }
}