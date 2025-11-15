import './information_page.dart';
import 'package:flutter/material.dart';

int counter = 0;

// ! it is StatefulWidget
class LoginPage extends StatefulWidget {
  const LoginPage({super.key});

  @override
  State<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends State<LoginPage> {
  TextEditingController nameController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(8.0),
          child: Column(
            children: [
              TextField(
                controller: nameController,
                decoration: const InputDecoration(
                    border: OutlineInputBorder(), hintText: "enter your name"),
              ),
              const SizedBox(height: 32),
              Text(
                "number is $counter",
                style: const TextStyle(fontSize: 32),
              ),
              const SizedBox(height: 32),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceAround,
                children: [
                  ElevatedButton(
                    onPressed: () {
                      counter++;
                      setState(
                          () {}); // it will refresh page after increment the counter
                    },
                    child: const Text("increment"),
                  ),
                  const SizedBox(height: 32),
                  ElevatedButton(
                    onPressed: () {
                      Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (context) => InformationPage(
                              name: nameController.text,
                            ),
                          ));
                    },
                    child: const Text("go to next page"),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
