import {ref} from "vue";
import {pb} from '@/pocketbase.js';
import {useRouter} from "vue-router";

// Make variable Singleton to not create multiple storages for currentUser
// Move it out of the scope of the composable to make it a singleton automatically based on JS import/export
const currentUser = ref(null);
const router = useRouter();
export function useLogin() {

  const login = async (email, password) => {
    const data = await pb.collection('users').authWithPassword(
      email,
      password,
    );
    currentUser.value = data.record;
    console.log(currentUser.value.id);
  }

  const logout = () => {
    currentUser.value = null;
    pb.authStore.clear();
  }

  // TODO add registering as a user
  const register = () => {
    // create user in PB -> triggers E-Mail verification
  }

  // TODO enable staying logged in
  if(!currentUser.value && pb.authStore.isValid && pb.authStore.record){
    currentUser.value = pb.authStore.record; // auf den user setzen der in der pb gespeichert ist

    console.log(currentUser.value);
  }
  // upon mounting this composable check if the user is still logged in through pb.authStore / token saved in localStorage
  // If yes set the saved user to be the current user

  return {
    currentUser,
    login,
    logout
  }
}
