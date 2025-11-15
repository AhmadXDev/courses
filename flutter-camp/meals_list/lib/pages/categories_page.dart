import 'package:flutter/material.dart';
import 'package:meals_list/constants/theme.dart';
import 'package:meals_list/data/global_data.dart';
import 'package:meals_list/extensions/push.dart';
import 'package:meals_list/pages/meals_page_bonus.dart';
// import 'package:meals_list/pages/meals_page.dart';
import 'package:meals_list/services/categories_api.dart';
import 'package:meals_list/widgets/text_widget.dart';

class CategoriesPage extends StatelessWidget {
  const CategoriesPage({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: barColor,
        title: const Text("Categories Page"),
        centerTitle: true,
      ),
      body: Padding(
          padding: const EdgeInsets.all(8),
          child: FutureBuilder(
              future: CategoriesApi().getData(),
              builder: (context, snapshot) {
                if (snapshot.connectionState == ConnectionState.waiting) {
                  return const Center(child: CircularProgressIndicator());
                } else if (snapshot.connectionState == ConnectionState.done) {
                  return ListView.builder(
                      itemCount: allCategories.length,
                      itemBuilder: (context, index) => Padding(
                            padding: const EdgeInsets.all(8.0),
                            child: InkWell(
                                onTap: () {
                                  context.push(MealsPageBonus(
                                      meal:
                                          allCategories[index].name.toString(),
                                      categoryImage:
                                          allCategories[index].image.toString(),
                                      categoryDescription: allCategories[index]
                                          .description
                                          .toString()));
                                },
                                child: SizedBox(
                                  height: 190,
                                  child: Card(
                                      child: Padding(
                                    padding: const EdgeInsets.all(8.0),
                                    child: Column(children: [
                                      // Column child 1
                                      Row(
                                        mainAxisAlignment:
                                            MainAxisAlignment.center,
                                        children: [
                                          Text("(${allCategories[index].id})"),
                                          Text("  ${allCategories[index].name}")
                                        ],
                                      ),

                                      // Column child
                                      const SizedBox(height: 30),

                                      // Column child 2
                                      Flexible(
                                          child: Row(children: [
                                        // Row child 1 |Image
                                        Image.network(
                                          allCategories[index].image.toString(),
                                          width: 100,
                                          height: 100,
                                          fit: BoxFit.cover,
                                        ),

                                        // Row Child | space between image and description
                                        const SizedBox(width: 10),

                                        // Column child 3 | description
                                        Expanded(
                                            child: Text(
                                          "${allCategories[index].description}",
                                          maxLines: 3,
                                          overflow: TextOverflow.ellipsis,
                                        ))
                                      ])),
                                    ]),
                                  )),
                                )),
                          ));
                } else {
                  return const TextWidget(text: "No Data!", size: 12);
                }
              })),
    );
  }
}
