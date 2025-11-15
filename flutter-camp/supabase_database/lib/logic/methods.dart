import 'package:flutter/material.dart';
import 'package:supabase_database/extensions/nav.dart';
import 'package:supabase_database/services/database.dart';

signupToPage(
  BuildContext context,
  TextEditingController emailController,
  TextEditingController passwordController,
  Widget target,
) async {
  try {
    await Database()
        .signup(email: emailController.text, password: passwordController.text);
    if (context.mounted) {
      context.push(target);
    }
  } catch (e) {
    if (context.mounted) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(e.toString()),
        duration: const Duration(seconds: 10),
      ));
    }
  }
}

loginToPage(
  BuildContext context,
  TextEditingController emailController,
  TextEditingController passwordController,
  Widget target,
) async {
  try {
    await Database()
        .login(email: emailController.text, password: passwordController.text);
    if (context.mounted) {
      context.push(target);
    }
  } catch (e) {
    if (context.mounted) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(
        content: Text(e.toString()),
        duration: const Duration(seconds: 10),
      ));
    }
  }
}

// insertToPage(
//   TextEditingController nameController,
//   TextEditingController ageController,
//   TextEditingController salaryController,
//   BuildContext context,
//   VoidCallback clearData,
// ) async {
//   try {
//     await Database().insertNewStudent(
//         name: nameController.text,
//         age: int.parse(ageController.text),
//         salary: double.parse(salaryController.text));
//     if (context.mounted) {
//       clearData();
//       setState(() {});
//       Navigator.pop(context);
//     }
//   } catch (e) {
//     if (context.mounted) {
//       ScaffoldMessenger.of(context).showSnackBar(SnackBar(
//         content: Text(e.toString()),
//         duration: const Duration(seconds: 10),
//       ));
//     }
//   }
// }
