<?php

namespace App\Http\Controllers\API\Gateways\TC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AvailableRatePlant extends Controller
{
  private string $dummy = '{
    "hotelCode": 1211,
    "currencyCode": "IDR",
    "languageCode": "EN_US",
    "hasMandatoryServices": false,
    "roomStays": [
      {
        "timeSpan": {
          "start": "2023-08-10",
          "end": "2023-08-11",
          "duration": 1
        },
        "roomTypes": [
          {
            "roomTypeCode": "511581",
            "roomTypeName": "Premier Non Smoking Twin",
            "description": "A 40sqm modern room with luxury touch in every corner. Our Premier Room offers the most exclusive room and bathroom design with separated standing high pressure rain shower cabin and bath tub. This room also equipped with a 46’ LED interactive high definition television with more than 60 channels, electric power curtain, and to ensure your comfort, we also provide exclusively customized luxury linen with Egyptian cotton bed sheet, duvet, pillows, all stuffed with down feathers which were selected only from the gooses’ neck to get you through the night. • Aromatherapy Associates London luxurious amenities • Exclusively designed bedding with 100% goose down pillows and pillow menu • Exclusively customized luxury linen with 320 thread-count Egyptian cotton • Daily in-room fruit amenity • Personalized in-room entertainment program with more than 60 television channels • Electric power curtain • Electronic proximity key access system • Room temperature and humidity control • Mood lighting controls • Luxurious marble bathroom with separate bathtub and high pressure rain shower • 24 hours on-call STAR service • 24 hours The Fitness Centre access • Express check-out service • 40 sqm",
            "sortOrder": 1,
            "averageRates": [
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "6689114",
                "ratePlanType": "Regular",
                "rate": 1800000.0,
                "discount": 540000.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 30.0,
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "19652309",
                  "labelText": "THG Loyalty Membership",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "MEGAR",
                "pmsRateExternalCode": "MEGAWEBRO",
                "ratePlanCategory": "Discount",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "7833792",
                "ratePlanType": "Regular",
                "rate": 2054100.0,
                "discount": 616230.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 30.0,
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "23509981",
                  "labelText": "THG Loyalty Membership",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "MGCC1",
                "pmsRateExternalCode": "MEGAWEB",
                "ratePlanCategory": "Discount",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "6317385",
                "ratePlanType": "Regular",
                "rate": 1530000.0,
                "discount": 76500.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 5.0,
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "4627142",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "ALLORO",
                "pmsRateExternalCode": "ALLORO",
                "ratePlanCategory": "Discount",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "7533597",
                "ratePlanType": "Regular",
                "rate": 1745985.0,
                "discount": 87299.25,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 5.0,
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "4627142",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "ALLO2",
                "pmsRateExternalCode": "ALLOBF",
                "ratePlanCategory": "Discount",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "3357470",
                "ratePlanType": "Regular",
                "rate": 1800000.0,
                "discount": 90000.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 5.0,
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "4627142",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "BARRO",
                "pmsRateExternalCode": "BARWEBRO",
                "ratePlanCategory": "Rack",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "3357471",
                "ratePlanType": "Regular",
                "rate": 2054100.0,
                "discount": 102705.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 5.0,
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "4627142",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "BAR",
                "pmsRateExternalCode": "BARWEB",
                "ratePlanCategory": "Rack",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "7772821",
                "ratePlanType": "Regular",
                "rate": 2854100.0,
                "discount": 142705.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 5.0,
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "4627142",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "STPLAT",
                "pmsRateExternalCode": "STPLAT",
                "ratePlanCategory": "Discount",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "734790",
                "ratePlanType": "Package",
                "rate": 3674100.0,
                "discount": 0.0,
                "showPromotionBanner": false,
                "geoPricingPromotion": false,
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {},
                "pmsPackageCode": "ROM18",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "728382",
                "ratePlanType": "Package",
                "rate": 2459100.0,
                "discount": 0.0,
                "showPromotionBanner": false,
                "geoPricingPromotion": false,
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {},
                "pmsPackageCode": "STAYD",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "738807",
                "ratePlanType": "Package",
                "rate": 2854100.0,
                "discount": 0.0,
                "showPromotionBanner": false,
                "geoPricingPromotion": false,
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {},
                "pmsPackageCode": "STPLA",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "720200",
                "ratePlanType": "Package",
                "rate": 2654100.0,
                "discount": 0.0,
                "showPromotionBanner": false,
                "geoPricingPromotion": false,
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {},
                "pmsPackageCode": "TSB",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              }
            ],
            "averageMemberRates": [],
            "roomFeatures": [
              {
                "amenityName": "People",
                "image": {
                  "type": "image",
                  "source": "images/icons/occupant.svg",
                  "sortOrder": 1
                },
                "quantity": 4,
                "type": "Occupancy"
              },
              {
                "amenityName": "Down/feather pillows",
                "image": {
                  "type": "image",
                  "source": "images/icons/bed.svg",
                  "sortOrder": 1
                },
                "quantity": 0,
                "type": "Beds"
              },
              {
                "amenityName": "Duvet",
                "image": {
                  "type": "image",
                  "source": "images/icons/bed.svg",
                  "sortOrder": 1
                },
                "quantity": 0,
                "type": "Beds"
              },
              {
                "amenityName": "Foam pillows",
                "image": {
                  "type": "image",
                  "source": "images/icons/bed.svg",
                  "sortOrder": 1
                },
                "quantity": 0,
                "type": "Beds"
              },
              {
                "amenityName": "Sq m / 431 Sq ft",
                "image": {
                  "type": "image",
                  "source": "images/icons/measuring-square.svg",
                  "sortOrder": 1
                },
                "quantity": 40,
                "type": "Size"
              }
            ],
            "amenities": [
              {
                "amenityName": "Air Conditioned",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/heater_and_air_conditioner.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Bathrobe",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/bathrobe.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Bathroom Telephone",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/telephone.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Bathtub",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/shower_in_bath.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Desk",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/desk.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Direct dial phone number",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/telephone.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Fire Alarm with Light",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/fire_alarm.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Local calls",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/telephone.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Long distance calls",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/telephone.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Minibar",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/Small_Mini-fridge_with_nothing_in_it.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Notepads",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/notecard.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Pens",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/notecard.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Shoe polisher",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/shoe_polish.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Shower",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/shower.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Tables and chairs",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/table_and_chairs.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Telephone",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/telephone.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Toilet",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/toilet.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Turndown Service",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/turndown_service.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "TV",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/widescreen_tv_or_computer.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Wake-up calls",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/alarm_clock.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Weight scale",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/scale_body_weight.svg",
                  "sortOrder": 0
                }
              }
            ],
            "mainImage": {
              "type": "main-image",
              "source": "/assets/hotel/1211/media/room/main-image/premier_twin_room_enhanced.png",
              "sortOrder": 0
            },
            "media": [
              {
                "type": "detail-image",
                "source": "/assets/hotel/1211/media/room/detail-image/detail-premier_twin_room_enhanced.png",
                "sortOrder": 0
              },
              {
                "type": "detail-image",
                "source": "/assets/hotel/1211/media/room/detail-image/premier_and_premier_club_-_bathroom_-_2_enhanced.jpg",
                "sortOrder": 1
              },
              {
                "type": "detail-image",
                "source": "/assets/hotel/1211/media/room/detail-image/premier_and_premier_club_-_bathroom_-_1_enhanced.jpg",
                "sortOrder": 2
              },
              {
                "type": "detail-image",
                "source": "/assets/hotel/1211/media/room/detail-image/aal-2_enhanced.jpg",
                "sortOrder": 3
              }
            ],
            "roomUpgradeOptions": [],
            "quantityRemaining": "2",
            "nightlyRates": [
              {
                "amtTotal": 1260000.0,
                "amtBeforeTax": 1260000.0,
                "date": "2023-08-10",
                "discount": 540000.0,
                "discountType": "Percent",
                "ratePlanCode": "6689114",
                "totalTax": 218677.686,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 218677.686,
                "baseRateWithoutAnyTax": 1041322.314,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 1437870.0,
                "amtBeforeTax": 1437870.0,
                "date": "2023-08-10",
                "discount": 616230.0,
                "discountType": "Percent",
                "ratePlanCode": "7833792",
                "totalTax": 249547.686,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 249547.686,
                "baseRateWithoutAnyTax": 1188322.314,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 1453500.0,
                "amtBeforeTax": 1453500.0,
                "date": "2023-08-10",
                "discount": 76500.0,
                "discountType": "Percent",
                "ratePlanCode": "6317385",
                "totalTax": 252260.3306,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 252260.3306,
                "baseRateWithoutAnyTax": 1201239.6694,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 1658685.75,
                "amtBeforeTax": 1658685.75,
                "date": "2023-08-10",
                "discount": 87299.25,
                "discountType": "Percent",
                "ratePlanCode": "7533597",
                "totalTax": 287871.0806,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 287871.0806,
                "baseRateWithoutAnyTax": 1370814.6694,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 1710000.0,
                "amtBeforeTax": 1710000.0,
                "date": "2023-08-10",
                "discount": 90000.0,
                "discountType": "Percent",
                "ratePlanCode": "3357470",
                "totalTax": 296776.8595,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 296776.8595,
                "baseRateWithoutAnyTax": 1413223.1405,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 1951395.0,
                "amtBeforeTax": 1951395.0,
                "date": "2023-08-10",
                "discount": 102705.0,
                "discountType": "Percent",
                "ratePlanCode": "3357471",
                "totalTax": 338671.8595,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 338671.8595,
                "baseRateWithoutAnyTax": 1612723.1405,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 2711395.0,
                "amtBeforeTax": 2711395.0,
                "date": "2023-08-10",
                "discount": 142705.0,
                "discountType": "Percent",
                "ratePlanCode": "7772821",
                "totalTax": 470572.686,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 470572.686,
                "baseRateWithoutAnyTax": 2240822.3140000002,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 3674100.0,
                "amtBeforeTax": 3674100.0,
                "date": "2023-08-10",
                "discount": 0.0,
                "discountType": null,
                "ratePlanCode": "734790",
                "totalTax": 637653.719,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 637653.719,
                "baseRateWithoutAnyTax": 3036446.281,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 2459100.0,
                "amtBeforeTax": 2459100.0,
                "date": "2023-08-10",
                "discount": 0.0,
                "discountType": null,
                "ratePlanCode": "728382",
                "totalTax": 0.0,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 0.0,
                "baseRateWithoutAnyTax": 2459100.0,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 2854100.0,
                "amtBeforeTax": 2854100.0,
                "date": "2023-08-10",
                "discount": 0.0,
                "discountType": null,
                "ratePlanCode": "738807",
                "totalTax": 0.0,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 0.0,
                "baseRateWithoutAnyTax": 2854100.0,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 2654100.0,
                "amtBeforeTax": 2654100.0,
                "date": "2023-08-10",
                "discount": 0.0,
                "discountType": null,
                "ratePlanCode": "720200",
                "totalTax": 460628.9256,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 460628.9256,
                "baseRateWithoutAnyTax": 2193471.0744,
                "outTotalExclusiveTaxes": 0.0
              }
            ],
            "displayUrgencyMessage": false,
            "roomExternalCode": "PRNT",
            "pmsRoomExternalCode": "PRNT",
            "available": true
          }
        ],
        "allRoomTypes": [
          {
            "roomTypeCode": "511581",
            "roomTypeName": "Premier Non Smoking Twin",
            "description": "A 40sqm modern room with luxury touch in every corner. Our Premier Room offers the most exclusive room and bathroom design with separated standing high pressure rain shower cabin and bath tub. This room also equipped with a 46’ LED interactive high definition television with more than 60 channels, electric power curtain, and to ensure your comfort, we also provide exclusively customized luxury linen with Egyptian cotton bed sheet, duvet, pillows, all stuffed with down feathers which were selected only from the gooses’ neck to get you through the night. • Aromatherapy Associates London luxurious amenities • Exclusively designed bedding with 100% goose down pillows and pillow menu • Exclusively customized luxury linen with 320 thread-count Egyptian cotton • Daily in-room fruit amenity • Personalized in-room entertainment program with more than 60 television channels • Electric power curtain • Electronic proximity key access system • Room temperature and humidity control • Mood lighting controls • Luxurious marble bathroom with separate bathtub and high pressure rain shower • 24 hours on-call STAR service • 24 hours The Fitness Centre access • Express check-out service • 40 sqm",
            "sortOrder": 1,
            "averageRates": [
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "6689114",
                "ratePlanType": "Regular",
                "rate": 1800000.0,
                "discount": 540000.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 30.0,
                "roomUpgradeList": [],
                "quantityRemaining": "2",
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "19652309",
                  "labelText": "THG Loyalty Membership",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "MEGAR",
                "pmsRateExternalCode": "MEGAWEBRO",
                "ratePlanCategory": "Discount",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "7833792",
                "ratePlanType": "Regular",
                "rate": 2054100.0,
                "discount": 616230.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 30.0,
                "roomUpgradeList": [],
                "quantityRemaining": "2",
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "23509981",
                  "labelText": "THG Loyalty Membership",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "MGCC1",
                "pmsRateExternalCode": "MEGAWEB",
                "ratePlanCategory": "Discount",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "6317385",
                "ratePlanType": "Regular",
                "rate": 1530000.0,
                "discount": 76500.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 5.0,
                "roomUpgradeList": [],
                "quantityRemaining": "2",
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "4627142",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "ALLORO",
                "pmsRateExternalCode": "ALLORO",
                "ratePlanCategory": "Discount",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "7533597",
                "ratePlanType": "Regular",
                "rate": 1745985.0,
                "discount": 87299.25,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 5.0,
                "roomUpgradeList": [],
                "quantityRemaining": "2",
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "4627142",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "ALLO2",
                "pmsRateExternalCode": "ALLOBF",
                "ratePlanCategory": "Discount",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "3357470",
                "ratePlanType": "Regular",
                "rate": 1800000.0,
                "discount": 90000.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 5.0,
                "roomUpgradeList": [],
                "quantityRemaining": "2",
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "4627142",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "BARRO",
                "pmsRateExternalCode": "BARWEBRO",
                "ratePlanCategory": "Rack",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "3357471",
                "ratePlanType": "Regular",
                "rate": 2054100.0,
                "discount": 102705.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 5.0,
                "roomUpgradeList": [],
                "quantityRemaining": "2",
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "4627142",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "BAR",
                "pmsRateExternalCode": "BARWEB",
                "ratePlanCategory": "Rack",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              },
              {
                "roomTypeCode": "511581",
                "ratePlanCode": "7772821",
                "ratePlanType": "Regular",
                "rate": 2854100.0,
                "discount": 142705.0,
                "showPromotionBanner": true,
                "geoPricingPromotion": false,
                "promotionPercentageDiscount": 5.0,
                "roomUpgradeList": [],
                "quantityRemaining": "2",
                "displayUrgencyMessage": false,
                "merchandisedPromoData": {
                  "promotionId": "4627142",
                  "saleText": "THG Loyalty Membership"
                },
                "rateExternalCode": "STPLAT",
                "pmsRateExternalCode": "STPLAT",
                "ratePlanCategory": "Discount",
                "isAbstract": false,
                "available": true,
                "allowWebRoomUpgrade": false,
                "xnightsDiscount": null,
                "xnightsDiscountType": null
              }
            ],
            "averageMemberRates": [],
            "roomFeatures": [
              {
                "amenityName": "People",
                "image": {
                  "type": "image",
                  "source": "images/icons/occupant.svg",
                  "sortOrder": 1
                },
                "quantity": 4,
                "type": "Occupancy"
              },
              {
                "amenityName": "Down/feather pillows",
                "image": {
                  "type": "image",
                  "source": "images/icons/bed.svg",
                  "sortOrder": 1
                },
                "quantity": 0,
                "type": "Beds"
              },
              {
                "amenityName": "Duvet",
                "image": {
                  "type": "image",
                  "source": "images/icons/bed.svg",
                  "sortOrder": 1
                },
                "quantity": 0,
                "type": "Beds"
              },
              {
                "amenityName": "Foam pillows",
                "image": {
                  "type": "image",
                  "source": "images/icons/bed.svg",
                  "sortOrder": 1
                },
                "quantity": 0,
                "type": "Beds"
              },
              {
                "amenityName": "Sq m / 431 Sq ft",
                "image": {
                  "type": "image",
                  "source": "images/icons/measuring-square.svg",
                  "sortOrder": 1
                },
                "quantity": 40,
                "type": "Size"
              }
            ],
            "amenities": [
              {
                "amenityName": "Air Conditioned",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/heater_and_air_conditioner.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Bathrobe",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/bathrobe.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Bathroom Telephone",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/telephone.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Bathtub",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/shower_in_bath.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Desk",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/desk.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Direct dial phone number",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/telephone.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Fire Alarm with Light",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/fire_alarm.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Local calls",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/telephone.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Long distance calls",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/telephone.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Notepads",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/notecard.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Pens",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/notecard.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Shoe polisher",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/shoe_polish.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Shower",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/shower.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Tables and chairs",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/table_and_chairs.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Telephone",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/telephone.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Toilet",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/toilet.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Turndown Service",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/turndown_service.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "TV",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/widescreen_tv_or_computer.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Wake-up calls",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/alarm_clock.svg",
                  "sortOrder": 0
                }
              },
              {
                "amenityName": "Weight scale",
                "sortOrder": 0,
                "isPremiumAmenity": false,
                "image": {
                  "type": "icon",
                  "source": "/assets/hotel/default/media/iconography/icon/scale_body_weight.svg",
                  "sortOrder": 0
                }
              }
            ],
            "mainImage": {
              "type": "main-image",
              "source": "/assets/hotel/1211/media/room/main-image/premier_twin_room_enhanced.png",
              "sortOrder": 0
            },
            "media": [
              {
                "type": "detail-image",
                "source": "/assets/hotel/1211/media/room/detail-image/detail-premier_twin_room_enhanced.png",
                "sortOrder": 0
              },
              {
                "type": "detail-image",
                "source": "/assets/hotel/1211/media/room/detail-image/premier_and_premier_club_-_bathroom_-_2_enhanced.jpg",
                "sortOrder": 1
              },
              {
                "type": "detail-image",
                "source": "/assets/hotel/1211/media/room/detail-image/premier_and_premier_club_-_bathroom_-_1_enhanced.jpg",
                "sortOrder": 2
              },
              {
                "type": "detail-image",
                "source": "/assets/hotel/1211/media/room/detail-image/aal-2_enhanced.jpg",
                "sortOrder": 3
              }
            ],
            "nightlyRates": [
              {
                "amtTotal": 1260000.0,
                "amtBeforeTax": 1260000.0,
                "date": "2023-08-10",
                "discount": 540000.0,
                "discountType": "Percent",
                "ratePlanCode": "6689114",
                "totalTax": 218677.686,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 218677.686,
                "baseRateWithoutAnyTax": 1041322.314,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 1437870.0,
                "amtBeforeTax": 1437870.0,
                "date": "2023-08-10",
                "discount": 616230.0,
                "discountType": "Percent",
                "ratePlanCode": "7833792",
                "totalTax": 249547.686,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 249547.686,
                "baseRateWithoutAnyTax": 1188322.314,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 1453500.0,
                "amtBeforeTax": 1453500.0,
                "date": "2023-08-10",
                "discount": 76500.0,
                "discountType": "Percent",
                "ratePlanCode": "6317385",
                "totalTax": 252260.3306,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 252260.3306,
                "baseRateWithoutAnyTax": 1201239.6694,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 1658685.75,
                "amtBeforeTax": 1658685.75,
                "date": "2023-08-10",
                "discount": 87299.25,
                "discountType": "Percent",
                "ratePlanCode": "7533597",
                "totalTax": 287871.0806,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 287871.0806,
                "baseRateWithoutAnyTax": 1370814.6694,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 1710000.0,
                "amtBeforeTax": 1710000.0,
                "date": "2023-08-10",
                "discount": 90000.0,
                "discountType": "Percent",
                "ratePlanCode": "3357470",
                "totalTax": 296776.8595,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 296776.8595,
                "baseRateWithoutAnyTax": 1413223.1405,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 1951395.0,
                "amtBeforeTax": 1951395.0,
                "date": "2023-08-10",
                "discount": 102705.0,
                "discountType": "Percent",
                "ratePlanCode": "3357471",
                "totalTax": 338671.8595,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 338671.8595,
                "baseRateWithoutAnyTax": 1612723.1405,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 2711395.0,
                "amtBeforeTax": 2711395.0,
                "date": "2023-08-10",
                "discount": 142705.0,
                "discountType": "Percent",
                "ratePlanCode": "7772821",
                "totalTax": 470572.686,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 470572.686,
                "baseRateWithoutAnyTax": 2240822.3140000002,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 3674100.0,
                "amtBeforeTax": 3674100.0,
                "date": "2023-08-10",
                "discount": 0.0,
                "discountType": null,
                "ratePlanCode": "734790",
                "totalTax": 637653.719,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 637653.719,
                "baseRateWithoutAnyTax": 3036446.281,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 2459100.0,
                "amtBeforeTax": 2459100.0,
                "date": "2023-08-10",
                "discount": 0.0,
                "discountType": null,
                "ratePlanCode": "728382",
                "totalTax": 0.0,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 0.0,
                "baseRateWithoutAnyTax": 2459100.0,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 2854100.0,
                "amtBeforeTax": 2854100.0,
                "date": "2023-08-10",
                "discount": 0.0,
                "discountType": null,
                "ratePlanCode": "738807",
                "totalTax": 0.0,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 0.0,
                "baseRateWithoutAnyTax": 2854100.0,
                "outTotalExclusiveTaxes": 0.0
              },
              {
                "amtTotal": 2654100.0,
                "amtBeforeTax": 2654100.0,
                "date": "2023-08-10",
                "discount": 0.0,
                "discountType": null,
                "ratePlanCode": "720200",
                "totalTax": 460628.9256,
                "totalExclusiveTaxes": 0.0,
                "totalInclusiveTaxes": 460628.9256,
                "baseRateWithoutAnyTax": 2193471.0744,
                "outTotalExclusiveTaxes": 0.0
              }
            ],
            "displayUrgencyMessage": false,
            "availableInventoryThreshold": 0,
            "roomExternalCode": "PRNT",
            "pmsRoomExternalCode": "PRNT",
            "available": true
          }
        ],
        "ratePlans": [
          {
            "ratePlanCode": "7833792",
            "ratePlanType": "Regular",
            "ratePlanName": "Special Rate for Mega Card  Holders",
            "leadRate": 2054100.0,
            "sortOrder": 2,
            "isAbstract": false,
            "discountRate": 616230.0,
            "cancellationPolicy": {
              "nonRefundable": false,
              "policyCode": "1271481",
              "policyDescription": "Maximum 24 hours prior to arrival to avoid a penalty of one night room and tax. 24 Hours prior to arrival to avoid a penalty of one night room and tax, except booking thru Trans Hotel Group app, will be NON REFUNDABLE.",
              "cancellationDeadlines": [
                {
                  "checkInDate": "2023-08-10",
                  "deadLineDate": "2023-08-09",
                  "deadLineHour": 15,
                  "deadLineDurationHours": 24
                }
              ]
            },
            "guaranteePolicy": {
              "policyCode": "1280876",
              "policyType": "Deposit",
              "policyDescription": "No Deposit Required, except booking thru Trans Hotel Group app, will be FULL PAYMENT in advance",
              "acceptTender": "Credit Card, Alternate Payments",
              "isInstallmentEnabled": false
            },
            "ratePlanDescription": "Special Room with breakfasts for 2 (two) adults and 2 (two) children below 6 YO rate for Bank Mega Credit Card Holders rate for Bank Mega credit card holders. Bank Mega rates are valid for payment using Bank Mega Credit Card except Bank Mega Silver card, corporate card, Mega Groserindo card, Mega Wholesale card, Fifa card and co-brand Bank Riau card. Failed to this, Best Available Rate is applied. Maximal discount for Bank Mega credit card are at IDR 2.000.000 and IDR 5.000.000 for Mega First Infinite Exclusive Offer for Mega First: Complimentary upgrade to Club Premier Room, based on room availability.",
            "confidential": false,
            "merchandisedPromoData": {
              "promotionId": "23509981",
              "labelText": "THG Loyalty Membership",
              "saleText": "THG Loyalty Membership"
            },
            "isAllowWebRoomUpgrade": false,
            "rateExternalCode": "MGCC1",
            "pmsRateExternalCode": "MEGAWEB",
            "ratePlanCategory": "Discount",
            "available": true,
            "multiRatePlan": false,
            "default": false
          },
          {
            "ratePlanCode": "3357470",
            "ratePlanType": "Regular",
            "ratePlanName": "Best Available Rate - Room Only ",
            "leadRate": 1800000.0,
            "sortOrder": 5,
            "isAbstract": false,
            "discountRate": 90000.0,
            "cancellationPolicy": {
              "nonRefundable": false,
              "policyCode": "1271481",
              "policyDescription": "Maximum 24 hours prior to arrival to avoid a penalty of one night room and tax. 24 Hours prior to arrival to avoid a penalty of one night room and tax, except booking thru Trans Hotel Group app, will be NON REFUNDABLE.",
              "cancellationDeadlines": [
                {
                  "checkInDate": "2023-08-10",
                  "deadLineDate": "2023-08-09",
                  "deadLineHour": 15,
                  "deadLineDurationHours": 24
                }
              ]
            },
            "guaranteePolicy": {
              "policyCode": "1280876",
              "policyType": "Deposit",
              "policyDescription": "No Deposit Required, except booking thru Trans Hotel Group app, will be FULL PAYMENT in advance",
              "acceptTender": "Credit Card, Alternate Payments",
              "isInstallmentEnabled": false
            },
            "ratePlanDescription": "Best Available Rate - Room Only",
            "confidential": false,
            "merchandisedPromoData": {
              "promotionId": "4627142",
              "saleText": "THG Loyalty Membership"
            },
            "isAllowWebRoomUpgrade": false,
            "rateExternalCode": "BARRO",
            "pmsRateExternalCode": "BARWEBRO",
            "ratePlanCategory": "Rack",
            "available": true,
            "multiRatePlan": false,
            "default": false
          },
          {
            "ratePlanCode": "3357471",
            "ratePlanType": "Regular",
            "ratePlanName": "Best Available Rate",
            "leadRate": 2054100.0,
            "sortOrder": 6,
            "isAbstract": false,
            "discountRate": 102705.0,
            "cancellationPolicy": {
              "nonRefundable": false,
              "policyCode": "1271481",
              "policyDescription": "Maximum 24 hours prior to arrival to avoid a penalty of one night room and tax. 24 Hours prior to arrival to avoid a penalty of one night room and tax, except booking thru Trans Hotel Group app, will be NON REFUNDABLE.",
              "cancellationDeadlines": [
                {
                  "checkInDate": "2023-08-10",
                  "deadLineDate": "2023-08-09",
                  "deadLineHour": 15,
                  "deadLineDurationHours": 24
                }
              ]
            },
            "guaranteePolicy": {
              "policyCode": "1280876",
              "policyType": "Deposit",
              "policyDescription": "No Deposit Required, except booking thru Trans Hotel Group app, will be FULL PAYMENT in advance",
              "acceptTender": "Credit Card, Alternate Payments",
              "isInstallmentEnabled": false
            },
            "ratePlanDescription": "Room rate inclusive breakfast for 2 (two) adults and 2 (two) children below 6 years old.",
            "confidential": false,
            "merchandisedPromoData": {
              "promotionId": "4627142",
              "saleText": "THG Loyalty Membership"
            },
            "isAllowWebRoomUpgrade": false,
            "rateExternalCode": "BAR",
            "pmsRateExternalCode": "BARWEB",
            "ratePlanCategory": "Rack",
            "available": true,
            "multiRatePlan": false,
            "default": false
          }
        ],
        "roomCategories": [],
        "packageCategories": [
          {
            "categoryCode": "2",
            "categoryName": "Family Package",
            "sortOrder": 1
          },
          {
            "categoryCode": "4",
            "categoryName": "Food and Beverage Package",
            "sortOrder": 2
          }
        ],
        "packageTypes": [
          {
            "packageCode": "734790",
            "packageName": "Romantic Escape",
            "description": "<p><span><strong>Romantic Escape&nbsp;&nbsp;<br> Celebrate each other with a romantic escape at The Trans Luxury Hotel! This exclusive offer including honeymoon room set-up, breakfast and romantic dinner at our iconic dining venue The 18<sup>th</sup>&nbsp;Restaurant and Lounge!&nbsp;</strong></span></p><p><span><strong>Inclusion :&nbsp;<br> •&nbsp;&nbsp; &nbsp;1 (one) night stay at our luxurious Premier Room<br> •&nbsp;&nbsp; &nbsp;Healthy breakfast for 2 (two) adults and 2 (two children below 6 (six) years old<br> •&nbsp;&nbsp; &nbsp;1 (one) time 5&nbsp;courses set menu&nbsp;romantic dinner at The 18<sup>th</sup>&nbsp;Restaurant and Lounge&nbsp;for 2 (two) persons&nbsp;<br> •&nbsp;&nbsp; &nbsp;Complimentary in-room minibar</strong></span></p><p><span><strong>Terms and conditions:<br> • &nbsp; &nbsp;Limited daily rooms availability<br> • &nbsp; &nbsp;Valid for reservation via www.thetranshotel.com&nbsp;</strong></span></p>",
            "sortOrder": 1,
            "leadRate": 3674100.0,
            "mainImage": {
              "type": "main-image",
              "source": "/assets/hotel/1211/media/package/main-image/romantic-escape2_320x320_enhanced.jpg",
              "sortOrder": 0
            },
            "media": [],
            "inclusions": [],
            "packagePolicies": [],
            "discountRate": 0.0,
            "categoryCode": "4",
            "confidential": false,
            "hotelPolicies": [
              {
                "nonRefundable": false,
                "policyCode": "1271481",
                "policyType": "Cancellation",
                "policyDescription": "Maximum 24 hours prior to arrival to avoid a penalty of one night room and tax. 24 Hours prior to arrival to avoid a penalty of one night room and tax, except booking thru Trans Hotel Group app, will be NON REFUNDABLE.",
                "cancellationDeadlines": [
                  {
                    "checkInDate": "2023-08-10",
                    "deadLineDate": "2023-08-09",
                    "deadLineHour": 15,
                    "deadLineDurationHours": 24
                  }
                ]
              },
              {
                "policyCode": "1280876",
                "policyType": "Deposit",
                "policyDescription": "No Deposit Required, except booking thru Trans Hotel Group app, will be FULL PAYMENT in advance",
                "acceptTender": "Credit Card, Alternate Payments",
                "isInstallmentEnabled": false
              }
            ],
            "hideDailyRate": false,
            "maxStay": 10,
            "minStay": 1,
            "packageStartDate": "2022-09-23",
            "packageEndDate": "2023-12-20",
            "hideMaxMinNight": false,
            "allowExtendedStay": false,
            "extendedStayMessage": "",
            "merchandisedPromoData": {},
            "isAllowWebRoomUpgrade": false,
            "pmsPackageCode": "ROM18",
            "available": true
          },
          {
            "packageCode": "728382",
            "packageName": "Stay and Dine",
            "description": "<strong>Stay and Dine</strong><br><br> Experience quality time with your loved ones with Stay and Dine package only at The Trans Luxury Hotel. We provide you an exclusive offer including breakfast, lunch or dinner, refreshing snack for your little one and plenty of fun activities in the hotel. With the implementation of health and safety protocols and this exciting package you and your family will be fully rested and recharged for the week ahead.<br> Inclusion :&nbsp; 1 (one) night stay at our hygiene and luxurious Premier Room Healthy breakfast for 2 (two) adults and 2 (two children below 6 (six) years old 1 (one) time lunch or dinner with food selections at The Lounge&nbsp;for 2 (two) persons 1 (one) glass of milkshake and cookies Complimentary in-room minibar Terms and conditions: • &nbsp; &nbsp;Booking and stay period are from now until 31 August 2023 • &nbsp; &nbsp;Limited daily rooms availability • &nbsp; &nbsp;Valid for reservation via www.thetranshotel.com&nbsp;",
            "sortOrder": 2,
            "leadRate": 2459100.0,
            "mainImage": {
              "type": "main-image",
              "source": "/assets/hotel/1211/media/package/main-image/stay_and_dine_kendra_320x320px_enhanced.jpg",
              "sortOrder": 0
            },
            "media": [],
            "inclusions": [],
            "packagePolicies": [],
            "discountRate": 0.0,
            "categoryCode": "4",
            "confidential": false,
            "hotelPolicies": [
              {
                "nonRefundable": false,
                "policyCode": "1271481",
                "policyType": "Cancellation",
                "policyDescription": "Maximum 24 hours prior to arrival to avoid a penalty of one night room and tax. 24 Hours prior to arrival to avoid a penalty of one night room and tax, except booking thru Trans Hotel Group app, will be NON REFUNDABLE.",
                "cancellationDeadlines": [
                  {
                    "checkInDate": "2023-08-10",
                    "deadLineDate": "2023-08-09",
                    "deadLineHour": 15,
                    "deadLineDurationHours": 24
                  }
                ]
              },
              {
                "policyCode": "1280876",
                "policyType": "Deposit",
                "policyDescription": "No Deposit Required, except booking thru Trans Hotel Group app, will be FULL PAYMENT in advance",
                "acceptTender": "Credit Card, Alternate Payments",
                "isInstallmentEnabled": false
              }
            ],
            "hideDailyRate": false,
            "maxStay": 10,
            "minStay": 1,
            "packageStartDate": "2021-09-03",
            "packageEndDate": "2023-08-31",
            "hideMaxMinNight": false,
            "allowExtendedStay": false,
            "extendedStayMessage": "",
            "merchandisedPromoData": {},
            "isAllowWebRoomUpgrade": false,
            "pmsPackageCode": "STAYD",
            "available": true
          },
          {
            "packageCode": "738807",
            "packageName": "Stay and Play Allo Package",
            "description": "<span><strong>Stay and Play Allo Package</strong></span><br><br><span>Enjoy our irresistible room package, which includes 1 night stay at our luxurious and hygiene Room, breakfast, Trans Studio Bandung tickets!. With the implementation of safety and health protocols at our complex as well as complete facilities, you and your family will experience a magical time with us.&nbsp;<br> This package is &nbsp;for ALLO Bank customers, valid for payment using ALLO PRIME on ALLO Bank Application. Failed to this, Best Available Rate is applied.<br> <br> Inclusive of:<br> • &nbsp; &nbsp;1 (one) night stay at our luxurius Club Premier Room<br> • &nbsp; &nbsp;Breakfast for 2 (two) adults and 2 (two) children below 6 (six) years &nbsp;old<br> • &nbsp; Exclusive &nbsp;access to The Club Lounge for &nbsp;2 (two) adults and 2 (two) children below 6 (six) years &nbsp;old &nbsp;(All-day Hight Tea I Evening Cocktail I Light Dinner)<br> • &nbsp; 4 &nbsp;Trans Studio Bandung tickets</span><br><br><strong>Terms and conditions:<br> • &nbsp; &nbsp;Booking and stay period are from now until 31&nbsp; August 2023<br> • &nbsp; &nbsp;Limited daily rooms availability<br> •&nbsp; &nbsp; V<span>alid for payment using ALLO PRIME on ALLO Bank Application. Failed to this, Best Available Rate is applied.</span></strong>",
            "sortOrder": 3,
            "leadRate": 2854100.0,
            "mainImage": {
              "type": "main-image",
              "source": "/assets/hotel/1211/media/package/main-image/trans_studio_bandung_-_2_enhanced_enhanced.jpg",
              "sortOrder": 0
            },
            "media": [],
            "inclusions": [],
            "packagePolicies": [],
            "discountRate": 0.0,
            "categoryCode": "2",
            "confidential": false,
            "hotelPolicies": [
              {
                "nonRefundable": false,
                "policyCode": "1271481",
                "policyType": "Cancellation",
                "policyDescription": "Maximum 24 hours prior to arrival to avoid a penalty of one night room and tax. 24 Hours prior to arrival to avoid a penalty of one night room and tax, except booking thru Trans Hotel Group app, will be NON REFUNDABLE.",
                "cancellationDeadlines": [
                  {
                    "checkInDate": "2023-08-10",
                    "deadLineDate": "2023-08-09",
                    "deadLineHour": 15,
                    "deadLineDurationHours": 24
                  }
                ]
              },
              {
                "policyCode": "1280876",
                "policyType": "Deposit",
                "policyDescription": "No Deposit Required, except booking thru Trans Hotel Group app, will be FULL PAYMENT in advance",
                "acceptTender": "Credit Card, Alternate Payments",
                "isInstallmentEnabled": false
              }
            ],
            "hideDailyRate": false,
            "maxStay": 10,
            "minStay": 1,
            "packageStartDate": "2023-05-31",
            "packageEndDate": "2023-08-31",
            "hideMaxMinNight": false,
            "allowExtendedStay": false,
            "extendedStayMessage": "",
            "merchandisedPromoData": {},
            "isAllowWebRoomUpgrade": false,
            "pmsPackageCode": "STPLA",
            "available": true
          },
          {
            "packageCode": "720200",
            "packageName": "Trans Studio Bandung Room Package",
            "description": "<p><strong>Trans Studio Bandung Room Package<br> <br> A fun and safe staycation at The Trans Luxury Hotel won’t feel complete without a day at one of the biggest in-door theme park in the world: Trans Studio Bandung. Enjoy our irresistible room package, which includes 1 night stay at our luxurious and hygiene Room, breakfast, Trans Studio Bandung tickets!.&nbsp;With the implementation of safety and health protocols at our complex as well as complete facilities, you and your family will experience a magical time with us.<br> <br> Inclusive of:<br> •&nbsp;&nbsp; &nbsp;1 (one) night stay at our luxurious and hygiene Room<br> •&nbsp;&nbsp; &nbsp;Breakfast for 2 (two) adults and 2 (two) children below 6 (six) years old<br> •&nbsp; &nbsp; 2&nbsp;Trans Studio Bandung tickets<br> <br> <br> Terms and conditions:<br> •&nbsp;&nbsp; &nbsp;Room is based on availability</strong><br> &nbsp;</p>",
            "sortOrder": 4,
            "leadRate": 2654100.0,
            "mainImage": {
              "type": "main-image",
              "source": "/assets/hotel/1211/media/package/main-image/trans_studio_bandung_room_package_irresistible_offer_enhanced.jpg",
              "sortOrder": 0
            },
            "media": [],
            "inclusions": [],
            "packagePolicies": [],
            "discountRate": 0.0,
            "categoryCode": "2",
            "confidential": false,
            "hotelPolicies": [
              {
                "nonRefundable": false,
                "policyCode": "1271481",
                "policyType": "Cancellation",
                "policyDescription": "Maximum 24 hours prior to arrival to avoid a penalty of one night room and tax. 24 Hours prior to arrival to avoid a penalty of one night room and tax, except booking thru Trans Hotel Group app, will be NON REFUNDABLE.",
                "cancellationDeadlines": [
                  {
                    "checkInDate": "2023-08-10",
                    "deadLineDate": "2023-08-09",
                    "deadLineHour": 15,
                    "deadLineDurationHours": 24
                  }
                ]
              },
              {
                "policyCode": "1280876",
                "policyType": "Deposit",
                "policyDescription": "No Deposit Required, except booking thru Trans Hotel Group app, will be FULL PAYMENT in advance",
                "acceptTender": "Credit Card, Alternate Payments",
                "isInstallmentEnabled": false
              }
            ],
            "hideDailyRate": false,
            "maxStay": 10,
            "minStay": 1,
            "packageStartDate": "2021-10-04",
            "packageEndDate": "2023-12-20",
            "hideMaxMinNight": false,
            "allowExtendedStay": false,
            "extendedStayMessage": "",
            "merchandisedPromoData": {},
            "isAllowWebRoomUpgrade": false,
            "pmsPackageCode": "TSB",
            "available": true
          }
        ],
        "mandatoryServices": []
      }
    ],
    "alternateHotels": null,
    "isPeStrikeThroughEnabled": true,
    "promotionApplied": false
  }';

  /**
   * Handle the incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function __invoke(Request $request)
  {
    //
    return response($this->dummy);
  }
}
