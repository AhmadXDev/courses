import 'package:flutter/material.dart';

class Page1 extends StatelessWidget {
  const Page1({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Colors.green,
      ),
      body: Column(
        children: [
          Container(
            width: 150,
            height: 150,
            color: Colors.red.shade300,
          ),
          Container(
            width: 250,
            height: 150,
            color: Colors.yellow.shade300,
          ),
          const Text("data"),
          const Icon(Icons.shape_line),
          ElevatedButton(
              onPressed: () {
                print("hello");
              },
              child: const Text("Break time"))
        ],
      ),
    );
  }
}
