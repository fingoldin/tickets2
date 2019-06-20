import cv2
import os

cover_path = "cover.jpg"
img_dir = "."

cover = cv2.imread(cover_path)
cwidth, cheight, _ = cover.shape

for path in os.listdir(img_dir):
    if "ticket" in path and path.endswith(".jpg"):
        img = cv2.imread(path)

        img[46:(46+cwidth),88:(88+cheight)] = cover

        cv2.imshow(path, img)
        
        c = cv2.waitKey(0) & 0xFF

        if c == ord('q'):
            break
        elif c == ord('y'):
            cv2.imwrite(path, img)
