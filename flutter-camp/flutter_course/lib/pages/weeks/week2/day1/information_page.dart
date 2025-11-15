import './intro_page.dart';
import 'package:flutter/material.dart';

class InformationPage extends StatelessWidget {
  final String name;
  const InformationPage({super.key, required this.name});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.blueAccent.shade200,
        title: Text("welcome $name"),
      ),
      body: ListView(
        children: [
          InkWell(
            onTap: () {
              // ignore: avoid_print
              print("Name is Ahmed");
              Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => const IntroPage(),
                  ));
            },
            child: Container(
              alignment: Alignment.center,
              margin: const EdgeInsets.all(20),
              height: 200,
              decoration: BoxDecoration(
                color: Colors.red.shade300,
                border: Border.all(
                  color: Colors.black,
                  width: 8,
                ),
              ),
              child: const Text(
                "Ahmed",
                style: TextStyle(fontSize: 24),
              ),
            ),
          ),
          Container(
            color: Colors.blue.shade300,
            height: 200,
            margin: const EdgeInsets.all(20),
          ),
          Container(
            color: Colors.green.shade300,
            height: 200,
            margin: const EdgeInsets.all(20),
          ),
          Container(
            color: Colors.orange.shade300,
            height: 200,
            margin: const EdgeInsets.all(20),
          ),
          Container(
            color: Colors.pink.shade300,
            height: 200,
            margin: const EdgeInsets.all(20),
          ),
        ],
      ),
    );
  }
}
