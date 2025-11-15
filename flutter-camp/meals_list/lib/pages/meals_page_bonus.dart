import 'package:flutter/material.dart';
import 'package:meals_list/constants/theme.dart';
import 'package:meals_list/data/global_data.dart';
import 'package:meals_list/services/meals_api.dart';
import 'package:meals_list/widgets/text_widget.dart';

class MealsPageBonus extends StatelessWidget {
  final String meal;
  final String categoryImage;
  final String categoryDescription;
  const MealsPageBonus(
      {super.key,
      required this.meal,
      required this.categoryImage,
      required this.categoryDescription});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
          backgroundColor: barColor, title: Text(meal), centerTitle: true),
      body: Padding(
        padding: const EdgeInsets.all(8.0),
        child: FutureBuilder(
            future: MealsApi().getData(meal),
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) {
                return const Center(child: CircularProgressIndicator());
              } else if (snapshot.connectionState == ConnectionState.done) {
                return SingleChildScrollView(
                    child: Center(
                  child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Center(child: Image.network(categoryImage)),

                        const SizedBox(height: 40),

                        const Center(
                            child: TextWidget(text: "Description", size: 30)),

                        const SizedBox(height: 10),

                        Center(child: Text(categoryDescription)),

                        const SizedBox(height: 20),

                        Center(
                            child: TextWidget(
                                text: "Meals(${allMeals.length})", size: 30)),

                        const SizedBox(height: 40),

                        // Meals List
                        SizedBox(
                            height: 200,
                            child: ListView.builder(
                                scrollDirection: Axis.horizontal,
                                itemCount: allMeals.length,
                                itemBuilder: (context, index) => Padding(
                                    padding: const EdgeInsets.all(8.0),
                                    child: InkWell(
                                      onTap: () {},
                                      child: Card(
                                        child: Padding(
                                            padding: const EdgeInsets.all(8.0),
                                            child: Center(
                                              child: Column(
                                                children: [
                                                  Image.network(
                                                    allMeals[index]
                                                        .image
                                                        .toString(),
                                                    width: 100,
                                                    height: 100,
                                                  ),
                                                  const SizedBox(width: 20),
                                                  Text(allMeals[index]
                                                      .name
                                                      .toString())
                                                ],
                                              ),
                                            )),
                                      ),
                                    ))))
                        // End of Meals List
                      ]),
                ));
              } else {
                return const Center(
                    child: TextWidget(text: "No Data", size: 24));
              }
            }),
      ),
    );
  }
}
