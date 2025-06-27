import { ref } from "vue";
import { pb } from "@/pocketbase.js";
import {useLogin} from "@/useLogin.js";

const allUsers = ref([]);

export function useUsers() {
  async function fetchUsers() {
    try {
      allUsers.value = await pb.collection('users').getFullList();

    } catch (error) {
      console.error("Error fetching tournaments:", error);
    }
  }



  return {
    allUsers,
    fetchUsers
  };
}
