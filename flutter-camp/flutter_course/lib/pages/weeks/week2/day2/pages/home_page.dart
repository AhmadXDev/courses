import '..//data/course_list.dart';
import '..//data/global_data.dart';
import '..//models/course.dart';
import 'package:flutter/material.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  @override
  void initState() {
    for (var element in courseList) {
      allCourses.add(Course.fromJson(element));
    }
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: const Color.fromRGBO(255, 196, 0, 1),
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: ListView.builder(
          itemCount: allCourses.length,
          itemBuilder: (context, index) => Card(
            child: Column(
              children: [
                Text(allCourses[index].title ?? "no title"),
                Image.network(
                  allCourses[index].image ??
                      "https://t4.ftcdn.net/jpg/04/70/29/97/360_F_470299797_UD0eoVMMSUbHCcNJCdv2t8B2g1GVqYgs.jpg",
                  width: 120,
                  height: 120,
                ),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: [
                    Column(
                      children: [
                        const Text("duration"),
                        Text(allCourses[index].duration!),
                      ],
                    ),
                    Column(
                      children: [
                        const Text("level"),
                        Text(allCourses[index].level!),
                      ],
                    ),
                    Column(
                      children: [
                        const Text("rating"),
                        Text(allCourses[index].rating.toString()),
                      ],
                    ),
                  ],
                )
              ],
            ),
          ),
        ),
      ),
    );
  }
}
