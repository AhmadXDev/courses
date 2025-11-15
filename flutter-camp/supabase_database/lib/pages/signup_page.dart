import 'package:flutter/material.dart';
import 'package:supabase_database/constants/spacing.dart';
import 'package:supabase_database/extensions/nav.dart';
import 'package:supabase_database/logic/methods.dart';
import 'package:supabase_database/pages/list_page.dart';
import 'package:supabase_database/pages/login_page.dart';
import 'package:supabase_database/widgets/text_input.dart';

class SignupPage extends StatefulWidget {
  const SignupPage({super.key});

  @override
  SignupPageState createState() => SignupPageState();
}

class SignupPageState extends State<SignupPage> {
  TextEditingController emailController = TextEditingController();
  TextEditingController passwordController = TextEditingController();

  @override
  void dispose() {
    emailController.dispose();
    passwordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.red.shade200,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          children: [
            const Text(
              "Signup",
              style: TextStyle(fontSize: 24),
            ),
            height24,
            TextInput(
              controller: emailController,
              labelText: "enter email",
              radiusCircular: 16.0,
            ),
            height24,
            TextInput(
              controller: passwordController,
              labelText: "enter password",
              isPassword: true,
              radiusCircular: 16.0,
            ),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Text("if you have already account"),
                TextButton(
                    onPressed: () {
                      context.pushOut(const LoginPage());
                    },
                    child: const Text("login")),
              ],
            ),
            height24,
            ElevatedButton(
                onPressed: () {
                  signupToPage(
                    context,
                    emailController,
                    passwordController,
                    const ListPage(),
                  );
                },
                style: ElevatedButton.styleFrom(
                  backgroundColor: const Color.fromARGB(255, 10, 67, 180),
                ),
                child: const Text(
                  "Signup",
                  style: TextStyle(fontSize: 18, color: Colors.white),
                ))
          ],
        ),
      ),
    );
  }
}
