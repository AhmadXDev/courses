import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:supabase_database/models/student.dart';

class Database {
  final supabase = Supabase.instance.client;

  Future<List<Student>> getData() async {
    var response = await supabase.from("student").select();

    List<Student> studentList = [];
    for (var element in response) {
      studentList.add(Student.fromJson(element));
    }
    // await Future.delayed(const Duration(seconds: 5));
    return studentList;
  }

  insertNewStudent(
      {required String name, required int age, required double salary}) async {
    await supabase.from("student").insert({
      "name": name,
      "age": age,
      "salary": salary,
    });
  }

  deleteUser({required int id}) async {
    await supabase.from("student").delete().eq("id", id);
  }

  updateUser(
      {required String name, required int id, required double salary}) async {
    await supabase
        .from("student")
        .update({"name": name, "salary": salary}).eq("id", id);
  }

  // ================================== For Authorization Table ==================================

  signup({required String email, required String password}) async {
    await supabase.auth.signUp(password: password, email: email);
  }

  login({required String email, required String password}) async {
    await supabase.auth.signInWithPassword(email: email, password: password);
  }
}
